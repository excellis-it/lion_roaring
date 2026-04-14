<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\RegisterAgreement;
use App\Models\UserArticleAcceptance;
use App\Models\UserRegisterAgreement;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
     * POST /register-agreement/sign
     * Records the authenticated user's register-agreement acceptance with signer details.
     * Idempotent — if the user already has a record, it returns the existing one.
     *
     * @bodyParam signer_name string required
     * @bodyParam signer_initials string optional
     * @bodyParam signature_image file optional  Persisted to users.signature if provided.
     */
    public function signRegisterAgreement(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'signer_name' => 'required|string|max:255',
            'signer_initials' => 'nullable|string|max:10',
            'signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = Auth::user();
        $existing = UserRegisterAgreement::where('user_id', $user->id)->orderByDesc('id')->first();
        if ($existing) {
            return response()->json([
                'status' => true,
                'message' => 'Agreement already signed.',
                'data' => $existing,
            ]);
        }

        $agreement = RegisterAgreement::orderByDesc('id')->first();

        $signaturePath = null;
        if ($request->hasFile('signature_image')) {
            $signaturePath = $request->file('signature_image')->store('register-agreements/signatures', 'public');
            $user->signature = $signaturePath;
            $user->save();
        }

        $record = UserRegisterAgreement::create([
            'user_id' => $user->id,
            'country_code' => $agreement->country_code ?? 'US',
            'signer_name' => $request->signer_name,
            'signer_initials' => $request->signer_initials,
            'pdf_path' => null,
            'agreement_title_snapshot' => $agreement->agreement_title ?? null,
            'agreement_description_snapshot' => $agreement->agreement_description ?? null,
            'checkbox_text_snapshot' => $agreement->checkbox_text ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Agreement signed.',
            'data' => $record,
        ], 201);
    }

    private function presentArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'country_code' => $article->country_code,
            'pdf_url' => $article->pdf ? Storage::url($article->pdf) : null,
            'checkbox_text' => $article->checkbox_text ?: 'I have read and agree to the Articles of Association',
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
        ];
    }
}
