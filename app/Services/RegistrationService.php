<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Mail\ActiveUserMail;
use App\Mail\NewUserRegistrationMail;
use App\Models\Country;
use App\Models\MembershipPromoCode;
use App\Models\MembershipTier;
use App\Models\Role;
use App\Models\SignupRule;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\UserRegisterAgreement;
use App\Models\UserSubscription;
use App\Models\UserType;
use App\Models\UserActivity;
use App\Services\MembershipPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Shared registration logic for web and mobile API (web registerCheck parity).
 */
class RegistrationService
{
    public function __construct(
        private readonly RegisterAgreementPreviewService $agreementPreview,
        private readonly MembershipTierRegistrationPolicy $tierRegistrationPolicy,
    ) {
    }

    public function generatedIdPart(): string
    {
        $todayCount = User::withTrashed()->whereDate('created_at', now()->toDateString())->count();
        $sequence = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

        return 'LR' . $sequence . now()->format('mdY');
    }

    /**
     * @return array{status: bool, http_code: int, body: array}
     */
    public function register(Request $request): array
    {
        $agreementToken = (string) $request->input('agreement_token');
        $idempotencyKey = $this->registrationIdempotencyKey($request);

        $existing = $this->findCompletedRegistration($request, $idempotencyKey);
        if ($existing) {
            return $this->successResponse($existing, $existing->status == 1);
        }

        $lock = Cache::lock('registration:' . $idempotencyKey, 120);
        if (!$lock->get()) {
            return [
                'status' => false,
                'http_code' => 409,
                'body' => [
                    'status' => false,
                    'message' => 'Registration is already being processed. Please wait.',
                ],
            ];
        }

        try {
            return $this->performRegistration($request, $agreementToken, $idempotencyKey);
        } finally {
            $lock->release();
        }
    }

    /**
     * @return array{status: bool, http_code: int, body: array}
     */
    private function performRegistration(Request $request, string $agreementToken, string $idempotencyKey): array
    {
        $existing = $this->findCompletedRegistration($request, $idempotencyKey);
        if ($existing) {
            return $this->successResponse($existing, $existing->status == 1);
        }

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255|unique:users',
            'generated_id_part' => 'required|string',
            'lion_roaring_id_suffix' => 'required|digits:4',
            'roar_id' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'ecclesia_id' => 'nullable',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'phone_number' => 'nullable|string|max:50',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'country' => 'required',
            'zip' => 'required',
            'email_confirmation' => 'required|same:email',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'password_confirmation' => 'required|same:password',
            'signature' => 'required|string',
            'tier_id' => 'required|exists:membership_tiers,id',
            'promo_code' => 'nullable|string|max:50',
            'agreement_token' => 'required|string|max:64',
            'phone_country_code_name' => 'nullable|string|max:10',
            'agree_accepted' => 'nullable|boolean',
            'stripeToken' => 'nullable|string',
            'payment_intent_id' => 'nullable|string',
            'setup_intent_id' => 'nullable|string',
            'billing_period' => 'nullable|in:monthly,yearly',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
            'signature.required' => 'Please provide your signature before submitting the form.',
            'tier_id.required' => 'Please select a membership tier.',
            'lion_roaring_id_suffix.required' => 'Please enter the last 4 digits for your Lion Roaring ID.',
            'lion_roaring_id_suffix.digits' => 'Lion Roaring ID suffix must be exactly 4 digits.',
            'agreement_token.required' => 'Please review and accept the registration agreement before registering.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $pending = $this->agreementPreview->getPendingForGuest(
                (string) $request->input('agreement_token')
            );

            if (!is_array($pending) || empty($pending['tmp_path']) || empty($pending['signer_name']) ||
                !Storage::disk('public')->exists($pending['tmp_path'])) {
                $validator->errors()->add('agreement_token', 'Please review and accept the registration agreement before registering.');

                return;
            }

            if (!$request->tier_id) {
                return;
            }

            $tier = MembershipTier::find($request->tier_id);
            if (!$tier) {
                return;
            }

            $tierLockError = $this->tierRegistrationPolicy->validateTierSelectable((int) $request->tier_id, $request);
            if ($tierLockError) {
                $validator->errors()->add('tier_id', $tierLockError);

                return;
            }

            $pricingType = $tier->pricing_type ?? 'amount';

            if ($pricingType === 'token') {
                if (!$request->boolean('agree_accepted')) {
                    $validator->errors()->add('agree_accepted', 'Please review and accept the tier agreement.');
                }

                return;
            }

            $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
            $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
            $finalPrice = $basePrice;
            if ($request->promo_code) {
                $promo = MembershipPromoCode::where('code', $request->promo_code)->first();
                if ($promo && $promo->canBeAppliedToTier($tier->id)) {
                    $discount = $promo->calculateDiscount($basePrice);
                    $finalPrice = max(0, $basePrice - $discount);
                }
            }

            if ($finalPrice > 0 && !$request->filled('stripeToken') && !$request->filled('payment_intent_id')) {
                $validator->errors()->add('payment', 'Payment is required for the selected tier.');
            } elseif ($basePrice > 0 && $finalPrice <= 0
                && !$request->filled('stripeToken')
                && !$request->filled('setup_intent_id')
                && !$request->filled('payment_intent_id')) {
                $validator->errors()->add('payment', 'Payment card details are required.');
            }
        });

        if ($validator->fails()) {
            return [
                'status' => false,
                'http_code' => 422,
                'body' => ['status' => false, 'errors' => $validator->errors()],
            ];
        }

        $fullLionRoaringId = $request->generated_id_part . $request->lion_roaring_id_suffix;
        if (User::where('lion_roaring_id', $fullLionRoaringId)->exists()) {
            return [
                'status' => false,
                'http_code' => 422,
                'body' => [
                    'status' => false,
                    'errors' => ['lion_roaring_id_suffix' => ['This Lion Roaring ID is already taken. Please try different last 4 digits.']],
                ],
            ];
        }

        $tier = MembershipTier::findOrFail($request->tier_id);
        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $promoCode = null;
        $discount = 0;
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $finalPrice = $basePrice;

        if ($request->filled('promo_code')) {
            $promoCode = MembershipPromoCode::where('code', $request->promo_code)->first();
            if ($promoCode && $promoCode->canBeAppliedToTier($tier->id)) {
                $discount = $promoCode->calculateDiscount($basePrice);
                $finalPrice = max(0, $basePrice - $discount);
            }
        }

        $paymentStatus = 'Pending';
        $transactionId = null;
        $paymentAmount = 0;

        if (($tier->pricing_type ?? 'amount') === 'amount' && $finalPrice > 0) {
            $paid = $this->processPayment($request, $finalPrice, $tier, $promoCode);
            if (!$paid['ok']) {
                return [
                    'status' => false,
                    'http_code' => 422,
                    'body' => ['status' => false, 'errors' => ['payment' => [$paid['message']]]],
                ];
            }
            $paymentStatus = 'Success';
            $transactionId = $paid['transaction_id'];
            $paymentAmount = $paid['amount'];
        }

        $uniqueNumber = rand(1000, 9999);
        $lrEmail = strtolower(trim($request->first_name))
            . strtolower(trim((string) $request->middle_name))
            . strtolower(trim($request->last_name))
            . $uniqueNumber . '@lionroaring.us';

        $phone = $request->phone;
        if (!$phone && $request->filled('phone_number')) {
            $phone = $request->phone_number;
        }

        $signupValidation = SignupRule::validateSignupData($request->all());

        $user = new User();
        $user->user_name = $request->user_name;
        $user->lion_roaring_id = $fullLionRoaringId;
        $user->roar_id = $request->roar_id;
        $user->ecclesia_id = $request->ecclesia_id ?: null;
        $user->email = $request->email;
        $user->personal_email = str_replace(' ', '', $lrEmail);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->phone = $phone;
        $user->phone_country_code_name = $request->phone_country_code_name;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->address2 = $request->address2;
        $user->country = $request->country;
        $user->zip = $request->zip;
        $user->password = bcrypt($request->password);
        $user->signature = $request->signature;
        $user->email_verified_at = now();
        $user->status = $signupValidation['user_should_be_active'] ? 1 : 0;
        $user->is_accept = $signupValidation['user_should_be_active'] ? 1 : 0;

        $domainCountry = Helper::getCountryByDomain();
        if ($domainCountry && $domainCountry->is_global) {
            $user->user_type = 'Global';
        } else {
            $user->user_type = 'Regional';
        }

        $user->save();

        $this->finalizeRegisterAgreement($user, (string) $request->agreement_token);

        $slug = Str::slug($user->user_name);
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('name', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $memberType = UserType::where('name', 'MEMBER_SOVEREIGN')->first();
        $newRole = Role::create([
            'name' => $slug,
            'type' => $memberType->type ?? 2,
            'is_ecclesia' => $memberType->is_ecclesia ?? 0,
            'guard_name' => 'web',
        ]);

        if (!empty($tier->permissions)) {
            $permissions = explode(',', $tier->permissions);
            $newRole->syncPermissions($permissions);
        }

        $user->assignRole($newRole->name);

        if ($memberType) {
            $user->user_type_id = $memberType->id;
            $user->save();
        }

        $userSubscription = new UserSubscription();
        $userSubscription->user_id = $user->id;
        $userSubscription->plan_id = $tier->id;
        $userSubscription->subscription_name = $tier->name;

        if (($tier->pricing_type ?? 'amount') === 'token') {
            $userSubscription->subscription_method = 'token';
            $userSubscription->subscription_price = $tier->life_force_energy_tokens;
            $userSubscription->life_force_energy_tokens = $tier->life_force_energy_tokens;
            $userSubscription->agree_accepted_at = now();
            $userSubscription->agree_description_snapshot = $tier->agree_description;
        } else {
            $userSubscription->subscription_method = 'amount';
            $userSubscription->subscription_price = $basePrice;
            $userSubscription->billing_period = $billingPeriod;
            $userSubscription->promo_code = $promoCode ? $promoCode->code : null;
            $userSubscription->discount_amount = $discount;
            $userSubscription->final_price = $paymentAmount;
            $userSubscription->life_force_energy_tokens = null;
            $userSubscription->agree_accepted_at = null;
            $userSubscription->agree_description_snapshot = null;
        }

        $durationMonths = ($tier->pricing_type ?? 'amount') === 'token'
            ? 12
            : MembershipPricing::durationMonthsFor($billingPeriod);
        $userSubscription->subscription_validity = $durationMonths;
        $userSubscription->subscription_start_date = now();
        $userSubscription->subscription_expire_date = now()->addMonths($durationMonths);
        $userSubscription->save();

        if ($paymentStatus === 'Success') {
            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $userSubscription->id;
            $payment->transaction_id = $transactionId;
            $payment->payment_method = $paymentAmount > 0 ? 'Stripe' : 'Promo';
            $payment->payment_amount = $paymentAmount;
            $payment->billing_period = $billingPeriod;
            $payment->promo_code = $promoCode ? $promoCode->code : null;
            $payment->discount_amount = $discount;
            $payment->payment_status = 'Success';
            $payment->save();

            if ($promoCode) {
                \App\Models\MembershipPromoUsage::create([
                    'promo_code_id' => $promoCode->id,
                    'user_id' => $user->id,
                    'subscription_id' => $userSubscription->id,
                    'discount_applied' => $discount,
                    'used_at' => now(),
                ]);
                $promoCode->incrementUsage();
            }
        }

        UserActivity::logActivity([
            'user_id' => $user->id,
            'activity_type' => 'REGISTER',
            'activity_description' => 'User registered with ' . $tier->name,
        ]);

        $this->sendRegistrationEmails($request, $user, $tier, $agreementToken);
        Cache::put($this->registrationCacheKey($idempotencyKey), $user->id, now()->addHours(2));

        return $this->successResponse($user, $user->status == 1);
    }

    private function registrationIdempotencyKey(Request $request): string
    {
        $parts = [
            (string) $request->input('agreement_token'),
            strtolower(trim((string) $request->input('email'))),
            (string) $request->input('payment_intent_id', ''),
        ];

        return hash('sha256', implode('|', $parts));
    }

    private function registrationCacheKey(string $idempotencyKey): string
    {
        return 'registration_complete:' . $idempotencyKey;
    }

    private function registrationEmailCacheKey(string $agreementToken): string
    {
        return 'registration_email_sent:' . $agreementToken;
    }

    private function findCompletedRegistration(Request $request, string $idempotencyKey): ?User
    {
        $cachedUserId = Cache::get($this->registrationCacheKey($idempotencyKey));
        if ($cachedUserId) {
            $user = User::find($cachedUserId);
            if ($user) {
                return $user;
            }
        }

        if ($request->filled('payment_intent_id')) {
            $payment = SubscriptionPayment::where('transaction_id', $request->payment_intent_id)
                ->where('payment_status', 'Success')
                ->first();
            if ($payment?->user) {
                return $payment->user;
            }
        }

        return null;
    }

    /**
     * @return array{status: bool, http_code: int, body: array}
     */
    private function successResponse(User $user, bool $isActive): array
    {
        $message = $isActive
            ? 'Thank you for registering! You can now login.'
            : 'Please wait for admin approval';

        return [
            'status' => true,
            'http_code' => 200,
            'body' => [
                'status' => true,
                'message' => $message,
                'user' => $user->fresh(),
            ],
        ];
    }

    /**
     * One welcome email to the registrant (web parity). Admin alerts go to super admins only,
     * never to the same address as the new member (prevents duplicate inbox on test accounts).
     */
    private function sendRegistrationEmails(Request $request, User $user, MembershipTier $tier, string $agreementToken): void
    {
        $emailSentKey = $this->registrationEmailCacheKey($agreementToken);
        if (Cache::has($emailSentKey)) {
            return;
        }

        $registrantEmail = strtolower(trim((string) $request->email));

        try {
            Mail::to($request->email)->send(new ActiveUserMail([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'type' => 'Activated',
                'status' => $user->status,
            ]));
            Cache::put($emailSentKey, true, now()->addHours(2));
        } catch (\Throwable $e) {
            Log::error('Registration welcome mail failed: ' . $e->getMessage());
        }

        try {
            $superAdminType = UserType::where('name', 'SUPER ADMIN')->first();
            if (!$superAdminType) {
                return;
            }

            $superAdmins = User::where('user_type_id', $superAdminType->id)
                ->where('status', 1)
                ->get();

            $adminMailData = [
                'new_user_name' => $request->first_name . ' ' . $request->last_name,
                'new_user_email' => $request->email,
                'new_user_username' => $request->user_name,
                'membership_tier' => $tier->name,
                'new_user_status' => $user->status,
                'registered_at' => now()->format('M d, Y h:i A'),
            ];

            foreach ($superAdmins as $admin) {
                if (strtolower(trim((string) $admin->email)) === $registrantEmail) {
                    continue;
                }
                Mail::to($admin->email)->send(new NewUserRegistrationMail($adminMailData));
            }
        } catch (\Throwable $e) {
            Log::error('Super admin registration notify failed: ' . $e->getMessage());
        }
    }

    /**
     * @return array{ok: bool, message?: string, transaction_id?: string, amount?: float}
     */
    private function processPayment(Request $request, float $finalPrice, MembershipTier $tier, ?MembershipPromoCode $promoCode): array
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            if ($request->filled('payment_intent_id')) {
                $intent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);
                if ($intent->status !== 'succeeded') {
                    return ['ok' => false, 'message' => 'Payment has not been completed.'];
                }
                $expected = (int) round($finalPrice * 100);
                if ((int) $intent->amount_received < $expected) {
                    return ['ok' => false, 'message' => 'Payment amount does not match the tier price.'];
                }

                return [
                    'ok' => true,
                    'transaction_id' => $intent->id,
                    'amount' => $finalPrice,
                ];
            }

            if ($request->filled('setup_intent_id')) {
                $verification = app(\App\Services\CheckoutPaymentService::class)
                    ->verifySetupIntent($request->setup_intent_id);
                if (!($verification['success'] ?? false)) {
                    return ['ok' => false, 'message' => 'Card verification not completed.'];
                }

                return [
                    'ok' => true,
                    'transaction_id' => $request->setup_intent_id,
                    'amount' => 0,
                ];
            }

            if ($request->filled('stripeToken') && $request->stripeToken !== 'free_tier') {
                if ($finalPrice > 0) {
                    $charge = \Stripe\Charge::create([
                        'amount' => (int) ($finalPrice * 100),
                        'currency' => 'usd',
                        'source' => $request->stripeToken,
                        'description' => 'Membership Registration - ' . $tier->name . ($promoCode ? ' (Promo: ' . $promoCode->code . ')' : ''),
                    ]);

                    if ($charge->status === 'succeeded') {
                        return [
                            'ok' => true,
                            'transaction_id' => $charge->id,
                            'amount' => $finalPrice,
                        ];
                    }

                    return ['ok' => false, 'message' => 'Payment failed.'];
                }

                $cardCheck = app(\App\Services\CheckoutPaymentService::class)->verifyCardToken($request->stripeToken);
                if (!($cardCheck['success'] ?? false)) {
                    return ['ok' => false, 'message' => $cardCheck['error'] ?? 'Payment card details are required.'];
                }

                return [
                    'ok' => true,
                    'transaction_id' => $cardCheck['transaction_id'],
                    'amount' => 0,
                ];
            }

            $basePrice = MembershipPricing::priceFor($tier, MembershipPricing::validatePeriod($request->input('billing_period')));
            if ($basePrice > 0) {
                return ['ok' => false, 'message' => 'Payment card details are required.'];
            }

            if ($finalPrice <= 0) {
                return ['ok' => true, 'transaction_id' => 'free_tier', 'amount' => 0];
            }

            return ['ok' => false, 'message' => 'Payment token is missing.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Payment error: ' . $e->getMessage()];
        }
    }

    private function finalizeRegisterAgreement(User $user, string $guestToken): void
    {
        $pending = $this->agreementPreview->getPendingForGuest($guestToken);
        if (!is_array($pending) || empty($pending['tmp_path']) ||
            !Storage::disk('public')->exists($pending['tmp_path'])) {
            return;
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

        UserRegisterAgreement::create([
            'user_id' => $user->id,
            'country_code' => $pending['country_code'] ?? 'US',
            'signer_name' => $pending['signer_name'] ?? ($user->first_name . ' ' . $user->last_name),
            'signer_initials' => $pending['signer_initials'] ?? null,
            'pdf_path' => $finalPath,
            'agreement_title_snapshot' => $pending['agreement_title_snapshot'] ?? null,
            'agreement_description_snapshot' => $pending['agreement_description_snapshot'] ?? null,
            'checkbox_text_snapshot' => $pending['checkbox_text_snapshot'] ?? null,
        ]);

        $this->agreementPreview->forgetPendingForGuest($guestToken);
    }

    /**
     * @return array{status: bool, http_code: int, body: array}
     */
    public function createRegistrationPaymentIntent(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'tier_id' => 'required|exists:membership_tiers,id',
            'promo_code' => 'nullable|string|max:50',
            'billing_period' => 'nullable|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'http_code' => 422,
                'body' => ['status' => false, 'errors' => $validator->errors()],
            ];
        }

        $tier = MembershipTier::findOrFail($request->tier_id);

        $tierLockError = $this->tierRegistrationPolicy->validateTierSelectable((int) $request->tier_id, $request);
        if ($tierLockError) {
            return [
                'status' => false,
                'http_code' => 422,
                'body' => ['status' => false, 'message' => $tierLockError],
            ];
        }

        if (($tier->pricing_type ?? 'amount') !== 'amount') {
            return [
                'status' => false,
                'http_code' => 422,
                'body' => ['status' => false, 'message' => 'This tier does not require card payment.'],
            ];
        }

        $billingPeriod = MembershipPricing::validatePeriod($request->input('billing_period'));
        $basePrice = MembershipPricing::priceFor($tier, $billingPeriod);
        $finalPrice = $basePrice;
        if ($request->filled('promo_code')) {
            $promo = MembershipPromoCode::where('code', $request->promo_code)->first();
            if ($promo && $promo->canBeAppliedToTier($tier->id)) {
                $finalPrice = max(0, $basePrice - $promo->calculateDiscount($basePrice));
            }
        }

        if ($finalPrice <= 0 && $basePrice > 0) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $setupIntent = \Stripe\SetupIntent::create([
                    'payment_method_types' => ['card'],
                    'metadata' => [
                        'context' => 'registration',
                        'tier_id' => (string) $tier->id,
                        'tier_name' => $tier->name,
                        'billing_period' => $billingPeriod,
                        'promo_code' => $request->promo_code ?? '',
                    ],
                ]);

                return [
                    'status' => true,
                    'http_code' => 200,
                    'body' => [
                        'status' => true,
                        'card_verification' => true,
                        'setup_intent_id' => $setupIntent->id,
                        'client_secret' => $setupIntent->client_secret,
                        'publishable_key' => config('services.stripe.key'),
                        'message' => 'Card verification required.',
                    ],
                ];
            } catch (\Throwable $e) {
                return [
                    'status' => false,
                    'http_code' => 500,
                    'body' => ['status' => false, 'message' => 'Could not verify card: ' . $e->getMessage()],
                ];
            }
        }

        if ($finalPrice <= 0) {
            return [
                'status' => true,
                'http_code' => 200,
                'body' => [
                    'status' => true,
                    'free' => true,
                    'message' => 'No payment required.',
                ],
            ];
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int) round($finalPrice * 100),
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'context' => 'registration',
                    'tier_id' => (string) $tier->id,
                    'tier_name' => $tier->name,
                    'billing_period' => $billingPeriod,
                ],
            ]);

            return [
                'status' => true,
                'http_code' => 200,
                'body' => [
                    'status' => true,
                    'free' => false,
                    'payment_intent_id' => $intent->id,
                    'client_secret' => $intent->client_secret,
                    'publishable_key' => config('services.stripe.key'),
                    'amount' => $finalPrice,
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'status' => false,
                'http_code' => 422,
                'body' => ['status' => false, 'message' => 'Could not create payment: ' . $e->getMessage()],
            ];
        }
    }
}
