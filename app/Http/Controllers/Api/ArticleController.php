<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\RegisterAgreement;
use App\Models\User;
use App\Models\UserArticleAcceptance;
use App\Models\UserRegisterAgreement;
use App\Services\RegisterAgreementPreviewService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * @group Articles & Agreements
 */
class ArticleController extends Controller
{
    /**
     * GET /articles/{id}
     * Returns an article with pdf URL and checkbox_text. Use `?id=latest` semantics by
     * calling /articles/latest (alias route) to get the newest article for the user's country.
     */
    public function show(int $id): JsonResponse
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['status' => false, 'message' => 'Article not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Article.',
            'data' => $this->presentArticle($article),
        ]);
    }

    /**
     * GET /articles/latest
     * @queryParam country_code string optional Two-letter country code.
     */
    public function latest(Request $request): JsonResponse
    {
        $country = $request->input('country_code');
        $article = Article::query()
            ->when($country, fn ($q) => $q->where('country_code', $country))
            ->orderByDesc('id')
            ->first();

        if (!$article) {
            return response()->json(['status' => false, 'message' => 'No article found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Article.',
            'data' => $this->presentArticle($article),
        ]);
    }

    /**
     * POST /articles/{id}/accept
     * Records the authenticated user's acceptance of an article. Idempotent — returns
     * the existing record if one already exists for that article.
     */
    public function accept(int $id): JsonResponse
    {
        $article = Article::find($id);
        if (!$article) {
            return response()->json(['status' => false, 'message' => 'Article not found.'], 404);
        }

        $acceptance = UserArticleAcceptance::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'article_id' => $article->id,
            ],
            [
                'country_code' => $article->country_code,
                'checkbox_text_snapshot' => $article->checkbox_text,
                'accepted_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Article accepted.',
            'data' => $acceptance,
        ]);
    }

    /**
     * GET /register-agreement (authenticated version with d/m/Y-formatted dates + seal + stewards)
     * @queryParam country_code string optional
     */
    public function registerAgreement(Request $request): JsonResponse
    {
        $country = $request->input('country_code');
        $agreement = RegisterAgreement::query()
            ->when($country, fn ($q) => $q->where('country_code', $country))
            ->orderByDesc('id')
            ->first();

        if (!$agreement) {
            return response()->json(['status' => false, 'message' => 'No register agreement found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Register agreement.',
            'data' => [
                'id' => $agreement->id,
                'country_code' => $agreement->country_code,
                'agreement_title' => $agreement->agreement_title,
                'agreement_description' => $agreement->agreement_description,
                'checkbox_text' => $agreement->checkbox_text,
                'seal_image' => $agreement->seal_image ? Storage::url($agreement->seal_image) : null,
                'steward_member_1' => $agreement->steward_member_1,
                'steward_member_2' => $agreement->steward_member_2,
                'effective_date' => $agreement->created_at ? Carbon::parse($agreement->created_at)->format('d/m/Y') : null,
                'last_updated' => $agreement->updated_at ? Carbon::parse($agreement->updated_at)->format('d/m/Y') : null,
            ],
        ]);
    }

    /**
     * POST /register-agreement/preview (public — pre-registration gate)
     *
     * @bodyParam signer_name string required
     * @bodyParam guest_token string optional Reuse token from a prior preview step
     * @bodyParam country_code string optional
     */
    public function previewRegisterAgreementGuest(Request $request, RegisterAgreementPreviewService $previewService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'signer_name' => 'required|string|max:255',
            'guest_token' => 'nullable|string|max:64',
            'country_code' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $guestToken = $request->input('guest_token') ?: (string) Str::uuid();
        $pending = $previewService->buildAndCacheForGuest(
            $guestToken,
            (string) $request->input('signer_name'),
            $request->input('country_code')
        );

        return response()->json([
            'status' => true,
            'guest_token' => $guestToken,
            'token' => $pending['token'],
            'pdf_url' => $previewService->absolutePdfUrl($pending['tmp_path']),
        ]);
    }

    /**
     * POST /user/register-agreement/preview
     * Generates a personalized agreement PDF preview (mobile / API parity with web sign-agreement).
     *
     * @bodyParam signer_name string required
     * @bodyParam country_code string optional
     */
    public function previewRegisterAgreement(Request $request, RegisterAgreementPreviewService $previewService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'signer_name' => 'required|string|max:255',
            'country_code' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $pending = $previewService->buildAndCacheForUser(
            $user->id,
            (string) $request->input('signer_name'),
            $request->input('country_code')
        );

        return response()->json([
            'status' => true,
            'token' => $pending['token'],
            'pdf_url' => $previewService->absolutePdfUrl($pending['tmp_path']),
        ]);
    }

    /**
     * POST /register-agreement/sign
     * Records the authenticated user's register-agreement acceptance with signer details.
     * Idempotent — if the user already has a record, it returns the existing one.
     *
     * @bodyParam signer_name string required
     * @bodyParam signer_initials string optional
     * @bodyParam signature string required when user has no signature yet  PNG data URL (web parity), e.g. data:image/png;base64,...
     * @bodyParam signature_image file optional Legacy multipart upload
     */
    public function signRegisterAgreement(Request $request, RegisterAgreementPreviewService $previewService): JsonResponse
    {
        $user = Auth::user();
        $needsSignature = empty($user->signature);

        $validator = Validator::make($request->all(), [
            'signer_name' => 'required|string|max:255',
            'signer_initials' => 'nullable|string|max:10',
            'signature' => ($needsSignature ? 'required' : 'nullable') . '|string',
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ], [
            'signature.required' => 'Please provide your signature before submitting.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        if ($needsSignature && !$request->filled('signature') && !$request->hasFile('signature_image')) {
            return response()->json([
                'status' => false,
                'message' => 'Please provide your signature before submitting.',
            ], 422);
        }

        $existing = UserRegisterAgreement::where('user_id', $user->id)->orderByDesc('id')->first();
        if ($existing) {
            $this->persistUserSignature($request, $user);

            return response()->json([
                'status' => true,
                'message' => 'Agreement already signed.',
                'data' => $existing,
            ]);
        }

        $pending = $previewService->getPendingForUser($user->id);
        if (!is_array($pending) || empty($pending['tmp_path']) || empty($pending['signer_name'])) {
            return response()->json([
                'status' => false,
                'message' => 'Please review and accept the agreement before submitting.',
            ], 422);
        }

        if (!Storage::disk('public')->exists($pending['tmp_path'])) {
            return response()->json([
                'status' => false,
                'message' => 'Agreement preview has expired. Please review the agreement again.',
            ], 422);
        }

        $token = $pending['token'] ?? (string) Str::uuid();
        $finalPath = "register-agreements/users/{$user->id}/agreement-{$token}.pdf";

        Storage::disk('public')->makeDirectory("register-agreements/users/{$user->id}");
        $moved = Storage::disk('public')->move($pending['tmp_path'], $finalPath);
        if (!$moved) {
            $content = Storage::disk('public')->get($pending['tmp_path']);
            Storage::disk('public')->put($finalPath, $content);
            Storage::disk('public')->delete($pending['tmp_path']);
        }

        $this->persistUserSignature($request, $user);

        $record = UserRegisterAgreement::create([
            'user_id' => $user->id,
            'country_code' => $pending['country_code'] ?? 'US',
            'signer_name' => $pending['signer_name'] ?? $request->signer_name,
            'signer_initials' => $pending['signer_initials'] ?? $request->signer_initials,
            'pdf_path' => $finalPath,
            'agreement_title_snapshot' => $pending['agreement_title_snapshot'] ?? null,
            'agreement_description_snapshot' => $pending['agreement_description_snapshot'] ?? null,
            'checkbox_text_snapshot' => $pending['checkbox_text_snapshot'] ?? null,
        ]);

        $previewService->forgetPendingForUser($user->id);

        return response()->json([
            'status' => true,
            'message' => 'Agreement signed successfully! Welcome to Lion Roaring PMA.',
            'data' => $record,
        ], 201);
    }

    /**
     * Store signature on the user — base64 data URL (web) or legacy file upload.
     */
    private function persistUserSignature(Request $request, User $user): void
    {
        if ($request->filled('signature')) {
            $user->signature = $request->input('signature');
            $user->save();

            return;
        }

        if ($request->hasFile('signature_image')) {
            $user->signature = $request->file('signature_image')->store('register-agreements/signatures', 'public');
            $user->save();
        }
    }

    private function presentArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'country_code' => $article->country_code,
            'pdf_url' => $article->pdf
                ? (new RegisterAgreementPreviewService())->absolutePdfUrl($article->pdf)
                : null,
            'checkbox_text' => $article->checkbox_text ?: 'I have read and agree to the Articles of Association',
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ];
    }
}
