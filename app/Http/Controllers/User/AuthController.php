<?php

namespace App\Http\Controllers\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Mail\AccountPendingApprovalMail;
use App\Mail\ActiveUserMail;
use App\Mail\RegistrationMail;
use App\Models\Country;
use App\Models\Ecclesia;
use App\Models\RegisterAgreement;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use App\Mail\OtpMail;
use App\Models\VerifyOTP;
use Illuminate\Support\Facades\Hash;
use App\Models\UserActivity;
use App\Models\MembershipTier;
use App\Models\UserSubscription;
use App\Models\SubscriptionPayment;
use App\Models\UserRegisterAgreement;
use App\Models\SignupRule;
use Stripe\StripeClient;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\UserType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function login()
    {
        $agreement = RegisterAgreement::orderBy('id', 'desc')->first();

        return view('user.auth.login')->with(compact('agreement'));
    }

    public function loginCheck(Request $request)
    {
        $request->validate([
            'user_name'    => 'required',
            'password' => 'required|min:8'
        ], [
            'user_name.required' => 'User name or email is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters'
        ]);

        // login by user_name or email
        $fieldType = filter_var($request->user_name, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
        $request->merge([$fieldType => $request->user_name]);

        $user = User::where($fieldType, $request->user_name)->first();

        $currentCode = strtoupper(Helper::getVisitorCountryCode());
        $country = Country::where('code', $currentCode)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->status == 1 && $user->is_accept == 1) {

                // dd($country->id, $user->country);
                if (($user->user_type == 'Regional') && ($country->id != $user->country)) {
                    return response()->json(['message' => 'You are not from ' . $country->name . '! Please change the country from dropdown.', 'status' => false]);
                }

                $otp = rand(1000, 9999);
                // $otp = 1234;
                $otp_verify = new VerifyOTP();
                $otp_verify->user_id = $user->id;
                $otp_verify->email = $user->email;
                $otp_verify->otp = $otp;
                $otp_verify->save();
                if ($request->has('remember')) {
                    $expire = time() + (86400 * 365 * 5); // 5 years
                    setcookie('email_user_name', $request->user_name, $expire, '/', '', false, true);
                    setcookie('password', $request->password, $expire, '/', '', false, true);
                } else {
                    // Clear cookies if remember me is unchecked
                    setcookie('email_user_name', '', time() - 3600, '/');
                    setcookie('password', '', time() - 3600, '/');
                }
                Session::put('user_id', $user->id);
                UserActivity::logActivity([
                    'user_id' => $user->id,
                    'activity_type' => 'LOGIN',
                    'activity_description' => 'User logged in',
                ]);
                try {
                    Mail::to($user->email)->send(new OtpMail($otp));
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Email server temporary unavailable. Please try later.', 'status' => false]);
                }
                return response()->json(['message' => 'Code sent to your email', 'status' => true, 'otp_required' => true]);
            } else {
                return response()->json(['message' => 'Your account is not active!', 'status' => false]);
            }
        } else {
            return response()->json(['message' => 'User name & password was invalid!', 'status' => false]);
        }
    }

    public function register()
    {
        // abort(404);
        // $eclessias = User::role('ECCLESIA')->orderBy('id', 'desc')->get();
        $eclessias = Ecclesia::orderBy('id', 'asc')->get();
        $countries = Country::all();
        $tiers = MembershipTier::with('benefits')->get();

        return view('user.auth.register')->with(compact('eclessias', 'countries', 'tiers'));
    }

    public function registerCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255|unique:users',
            'lion_roaring_id' => 'required|digits:4',
            'roar_id' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'ecclesia_id' => 'nullable',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'zip' => 'required',
            'email_confirmation' => 'required|same:email',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'password_confirmation' => 'required|same:password',
            'signature' => 'required|string',
            'tier_id' => 'required|exists:membership_tiers,id',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
            'signature.required' => 'Please provide your signature before submitting the form.',
            'tier_id.required' => 'Please select a membership tier.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $pending = $request->session()->get('pending_register_agreement');
            if (!is_array($pending) || empty($pending['tmp_path']) || empty($pending['signer_name'])) {
                $validator->errors()->add('register_agreement', 'Please review and accept the registration agreement before registering.');
                return;
            }

            if (!Storage::disk('public')->exists($pending['tmp_path'])) {
                $validator->errors()->add('register_agreement', 'Agreement preview has expired. Please review the agreement again.');
                return;
            }

            if (!$request->tier_id) {
                return;
            }

            $tier = MembershipTier::find($request->tier_id);

            if (!$tier) {
                return;
            }

            $pricingType = $tier->pricing_type ?? 'amount';

            if ($pricingType === 'token') {
                if (!$request->boolean('agree_accepted')) {
                    $validator->errors()->add('agree_accepted', 'Please review and accept the tier agreement.');
                }
                return;
            }

            // Amount based tiers
            if (floatval($tier->cost) > 0 && !$request->stripeToken) {
                $validator->errors()->add('stripeToken', 'Payment token is missing. Please try again.');
            }
        });

        if ($validator->fails()) {
            Log::error('' . $validator->errors()->first());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $phone_number = $request->full_phone_number;
        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->count();
        if ($check > 0) {
            return redirect()->back()->withErrors(['phone_number' => 'Phone number already exists'])->withInput();
        }

        $uniqueNumber = rand(1000, 9999);
        $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';

        $currentCode = strtoupper(Helper::getVisitorCountryCode());
        $country = Country::where('code', $currentCode)->first();
        if ($country && $country->id != $request->country) {
            $validator->errors()->add('country', 'Now you are registered from ' . $country->name . '! Please change the country from dropdown.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tier = MembershipTier::find($request->tier_id);
        $payment_status = 'Pending';
        $transaction_id = null;
        $payment_amount = 0;

        // Process Payment
        if (($tier->pricing_type ?? 'amount') === 'amount' && floatval($tier->cost) > 0) {
            try {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $charge = \Stripe\Charge::create([
                    'amount' => $tier->cost * 100,
                    'currency' => 'usd',
                    'source' => $request->stripeToken,
                    'description' => 'Membership Registration - ' . $tier->name,
                ]);

                if ($charge->status == 'succeeded') {
                    $payment_status = 'Success';
                    $transaction_id = $charge->id;
                    $payment_amount = $tier->cost;
                } else {
                    return redirect()->back()->withErrors(['stripeToken' => 'Payment failed.'])->withInput();
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['stripeToken' => 'Payment error: ' . $e->getMessage()])->withInput();
            }
        }

        // Validate signup data against field rules
        $signupValidation = SignupRule::validateSignupData($request->all());

        // dd($signupValidation);
        // Generate Lion Roaring ID: LR + 4-digit sequence + MMDDYYYY + user input (last 4 digits)
        $todayCount = User::withTrashed()->whereDate('created_at', now()->toDateString())->count();
        $computerGenerated = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
        $currentDate = now()->format('mdY'); // MMDDYYYY
        $userInputDigits = str_pad($request->lion_roaring_id, 4, '0', STR_PAD_LEFT);
        $fullLionRoaringId = 'LR' . $computerGenerated . $currentDate . $userInputDigits;

        // Ensure uniqueness (extremely unlikely to fail with sequence, but safer)
        while (User::where('lion_roaring_id', $fullLionRoaringId)->exists()) {
            $todayCount++;
            $computerGenerated = str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
            $fullLionRoaringId = 'LR' . $computerGenerated . $currentDate . $userInputDigits;
        }

        $user = new User();
        $user->user_name = $request->user_name;
        $user->lion_roaring_id = $fullLionRoaringId;
        $user->roar_id = $request->roar_id;
        $user->ecclesia_id = $request->ecclesia_id;
        $user->email = $request->email;
        $user->personal_email = $lr_email ? str_replace(' ', '', $lr_email) : null;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->phone = $request->country_code ? '+' . $request->country_code . ' ' . $request->phone_number : $request->phone_number;
        $user->phone_country_code_name = $request->phone_country_code_name;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->address2 = $request->address2;
        $user->country = $request->country;
        $user->zip = $request->zip;
        $user->password = bcrypt($request->password);
        $user->signature = $request->signature;
        $user->email_verified_at = now();

        // Set user status based on signup field validation
        // If all critical rules passed, user is active (1), otherwise inactive (0)
        $user->status = $signupValidation['user_should_be_active'] ? 1 : 0;
        $user->is_accept = $signupValidation['user_should_be_active'] ? 1 : 0;
        $user->save();

        // Finalize & persist registration agreement PDF against this user
        $pending = $request->session()->get('pending_register_agreement');
        if (is_array($pending) && !empty($pending['tmp_path']) && Storage::disk('public')->exists($pending['tmp_path'])) {
            $token = $pending['token'] ?? (string) \Illuminate\Support\Str::uuid();
            $finalPath = "register-agreements/users/{$user->id}/agreement-{$token}.pdf";

            // Ensure directory exists
            Storage::disk('public')->makeDirectory("register-agreements/users/{$user->id}");

            $moved = Storage::disk('public')->move($pending['tmp_path'], $finalPath);
            if (!$moved) {
                // Fallback: copy+delete
                $content = Storage::disk('public')->get($pending['tmp_path']);
                Storage::disk('public')->put($finalPath, $content);
                Storage::disk('public')->delete($pending['tmp_path']);
            }

            UserRegisterAgreement::create([
                'user_id' => $user->id,
                'country_code' => $pending['country_code'] ?? 'US',
                'signer_name' => $pending['signer_name'] ?? ($request->first_name . ' ' . $request->last_name),
                'signer_initials' => $pending['signer_initials'] ?? null,
                'pdf_path' => $finalPath,
                'agreement_title_snapshot' => $pending['agreement_title_snapshot'] ?? null,
                'agreement_description_snapshot' => $pending['agreement_description_snapshot'] ?? null,
                'checkbox_text_snapshot' => $pending['checkbox_text_snapshot'] ?? null,
            ]);

            $request->session()->forget('pending_register_agreement');
        }


        // Create a unique slug for the role name based on user_name (as per store partner)
        $slug = Str::slug($user->user_name);

        // Ensure slug is unique in roles table
        $originalSlug = $slug;
        $counter = 1;
        while (Role::where('name', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Find the default Member user type (as baseline)
        $memberType = UserType::where('name', 'MEMBER_NON_SOVEREIGN')->first();

        // Create the new role for this specific user
        $newRole = Role::create([
            'name' => $slug,
            'type' => $memberType->type ?? 2,
            'is_ecclesia' => $memberType->is_ecclesia ?? 0,
            'guard_name' => 'web'
        ]);

        // Assign permissions from membership tier to this new role
        if (!empty($tier->permissions)) {
            $permissions = explode(',', $tier->permissions);
            $newRole->syncPermissions($permissions);
        }

        // Assign the newly created role to the user
        $user->assignRole($newRole->name);

        // Set user_type_id and ensure user is saved with it
        if ($memberType) {
            $user->user_type_id = $memberType->id;
            $user->save();
        }

        // Create Subscription
        $user_subscription = new UserSubscription();
        $user_subscription->user_id = $user->id;
        $user_subscription->plan_id = $tier->id;
        $user_subscription->subscription_name = $tier->name;

        if (($tier->pricing_type ?? 'amount') === 'token') {
            $user_subscription->subscription_method = 'token';
            $user_subscription->subscription_price = $tier->life_force_energy_tokens;
            $user_subscription->life_force_energy_tokens = $tier->life_force_energy_tokens;
            $user_subscription->agree_accepted_at = now();
            $user_subscription->agree_description_snapshot = $tier->agree_description;
        } else {
            $user_subscription->subscription_method = 'amount';
            $user_subscription->subscription_price = $tier->cost;
            $user_subscription->life_force_energy_tokens = null;
            $user_subscription->agree_accepted_at = null;
            $user_subscription->agree_description_snapshot = null;
        }

        $user_subscription->subscription_validity = 12; // 12 months by default
        $user_subscription->subscription_start_date = now();
        $user_subscription->subscription_expire_date = now()->addYear();
        $user_subscription->save();

        if ($payment_status == 'Success') {
            $payment = new SubscriptionPayment();
            $payment->user_id = $user->id;
            $payment->user_subscription_id = $user_subscription->id;
            $payment->transaction_id = $transaction_id;
            $payment->payment_method = 'Stripe';
            $payment->payment_amount = $payment_amount;
            $payment->payment_status = 'Success';
            $payment->save();
        }

        // $maildata = [
        //     'name' => $request->first_name . ' ' . $request->last_name,
        // ];

        UserActivity::logActivity([
            'user_id' => $user->id,
            'activity_type' => 'REGISTER',
            'activity_description' => 'User registered with ' . $tier->name,
        ]);

        //  Mail::to($request->email)->send(new AccountPendingApprovalMail($maildata));

        $maildata = [
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'type' => 'Activated',
            'status' => $user->status,
        ];

        if ($user->status == 1) {
            Mail::to($request->email)->send(new ActiveUserMail($maildata));
            return redirect()->route('home')->with('message', 'Thank you for registering! You can now login');
        } else {
            Mail::to($request->email)->send(new ActiveUserMail($maildata));
            return redirect()->route('home')->with('message', 'Please wait for admin approval');
        }
    }

    public function logout()
    {
        // Log user activity before logout
        UserActivity::logActivity([
            'user_id' => auth()->id(),
            'activity_type' => 'LOGOUT',
            'activity_description' => 'User logged out',
        ]);
        auth()->logout();
        return redirect()->route('home')->with('message', 'Logout success');
    }

    public function registerValidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255|unique:users',
            'lion_roaring_id' => 'required|digits:4',
            'roar_id' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'ecclesia_id' => 'nullable',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'zip' => 'required',
            'email_confirmation' => 'required|same:email',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'password_confirmation' => 'required|same:password',
            'signature' => 'required|string',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
            'signature.required' => 'Please provide your signature before submitting the form.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        $phone_number = $request->full_phone_number;
        if (!$phone_number) {
            // In case full_phone_number is not populated by JS correctly for this check, check phone_number
            // But existing code uses full_phone_number from request, which JS should send.
            $phone_number = $request->phone_number;
        }

        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->count();
        if ($check > 0) {
            return response()->json(['status' => false, 'errors' => ['phone_number' => ['Phone number already exists']]]);
        }

        $currentCode = strtoupper(Helper::getVisitorCountryCode());
        $country = Country::where('code', $currentCode)->first();
        if ($country && $country->id != $request->country) {
            return response()->json(['status' => false, 'errors' => ['country' => ['Now you are registered from ' . $country->name . '! Please change the country from dropdown.']]]);
        }

        return response()->json(['status' => true]);
    }

    public function getStates(Request $request)
    {
        $states = State::where('country_id', $request->country)->get();
        return response()->json($states);
    }
}
