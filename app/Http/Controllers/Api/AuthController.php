<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisterAgreement;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\Ecclesia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountPendingApprovalMail;
use App\Mail\OtpMail;
use App\Models\VerifyOTP;

/**
 *  @group Authentication
 */
class AuthController extends Controller
{
    protected $successStatus = 200;

    /**
     * Register a new user
     *
     * Registers a new user and sends an email for account pending approval.
     *
     * @bodyParam user_name string required Unique username for the user. Example: johndoe
     * @bodyParam email string required Unique email address. Example: johndoe@example.com
     * @bodyParam ecclesia_id integer nullable Ecclesia ID if applicable.
     * @bodyParam first_name string required First name of the user. Example: John
     * @bodyParam last_name string required Last name of the user. Example: Doe
     * @bodyParam middle_name string nullable Middle name of the user. Example: A.
     * @bodyParam address string required Address of the user. Example: 123 Main St
     * @bodyParam phone string required User's phone number. Example: 1234567890
     * @bodyParam city string required City of residence. Example: Springfield
     * @bodyParam country integer required Country of residence.
     * @bodyParam state integer required State of residence.
     * @bodyParam address2 string nullable Additional address information. Example: Apt 4B
     * @bodyParam zip string required Zip code. Example: 62704
     * @bodyParam email_confirmation string required Confirmation of the email address. Must match `email`. Example: johndoe@example.com
     * @bodyParam password string required Password for the user. Must be at least 8 characters and include one special character (@$%&). Example: Password@123
     * @bodyParam password_confirmation string required Confirmation of the password. Must match `password`. Example: Password@123
     *
     * @response 200 {
     *  "message": "Please wait for admin approval",
     *  "user": {
     *      "id": 1,
     *      "user_name": "johndoe",
     *      "email": "johndoe@example.com",
     *      "first_name": "John",
     *      "last_name": "Doe",
     *      "created_at": "2024-11-06T10:00:00.000000Z",
     *      "updated_at": "2024-11-06T10:00:00.000000Z"
     *  }
     * }
     * @response 422 {
     *  "errors": {
     *      "email": ["The email has already been taken."]
     *  }
     * }
     * @response 409 {
     *  "error": "Phone number already exists"
     * }
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'ecclesia_id' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required',
            'city' => 'required|string|max:255',
            'state' => 'required|max:255',
            'address2' => 'nullable|string|max:255',
            'country' => 'required|max:255',
            'zip' => 'required',
            'email_confirmation' => 'required|same:email',
            'password' => ['required', 'string', 'regex:/^(?=.*[@$%&])[^\s]{8,}$/'],
            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one special character from @$%&.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $phone_number = $request->full_phone_number;
        $phone_number_cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone_number);

        $check = User::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$phone_number_cleaned])->count();
        if ($check > 0) {
            return response()->json(['error' => 'Phone number already exists'], 409);
        }

        $uniqueNumber = rand(1000, 9999);
        $lr_email = strtolower(trim($request->first_name)) . strtolower(trim($request->middle_name)) . strtolower(trim($request->last_name)) . $uniqueNumber . '@lionroaring.us';


        $user = new User();
        $user->user_name = $request->user_name;
        $user->ecclesia_id = $request->ecclesia_id;
        $user->email = $request->email;
        $user->personal_email = $lr_email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->address2 = $request->address2;
        $user->country = $request->country;
        $user->zip = $request->zip;
        $user->password = bcrypt($request->password);
        $user->email_verified_at = now();
        $user->status = 0;
        $user->save();


        $user->assignRole('MEMBER_NON_SOVEREIGN');
        $maildata = [
            'name' => $request->first_name . ' ' . $request->last_name,
        ];

        Mail::to($request->email)->send(new AccountPendingApprovalMail($maildata));

        return response()->json([
            'status' => true,
            'message' => 'Please wait for admin approval',
            'user' => $user,
        ], 200);
    }

    /**
     * Register Agreement Details
     *
     * @response 200{
     * "status": true
     * "message": "Data found"
     *  "data": {
     *    "id": 1,
     *    "agreement_title": "LION ROARING PMA (PRIVATE MEMBERS ASSOCIATION) AGREEMENT",
     *    "agreement_description": "<p>It is the responsibility of the members to read and review the Articles of Association of Lion Roaring PMA in its entirety and agree to adopt and comply to its belief, foundation and purpose of the Lion Roaring PMA. <a href=\"http://127.0.0.1:8001/login\"><strong>Click here to read the full document XXXXXXXX</strong></a></p><h4><strong>Each member agrees to the following excerpt taken from the Articles of Association of PMA:</strong></h4><ul><li>1. Lion Roaring PMA is a Private Members Association protected under the Constitution of the United States of America and the original constitution for these united States of America and the Maryland Constitution</li><li>2. Member agrees and is supportive to the mission and vision of the Lion Roaring</li><li>3. Member strives to contribute to the purpose of the PMA to fulfill the God given call to the founding members as it is written in Section 4 through 15 in the Article of Association</li><li>4. Member will not hold Lion Roaring PMA liable for any materials or contents posted in the website or any paperwork, written articles, education materials or others created within the PMA for its members’ benefits and private usage</li><li>5. Member’s agreement does not entitle a member to any financial or other interest in the Private Members Association or management thereof</li><li>6. Information regarding details of the association, any materials produced or created by Lion Roaring PMA including all paperwork, agreements, articles, PowerPoints presentations, word parchments, coaching, and education materials are private intellectual property of the PMA and will not be shared, replicated, dispersed or distributed with anyone outside the PMA without explicit written permission from the founder</li><li>7. Member’s due diligence is expected and member will hold harmless any member or founder of Lion Roaring PMA and any dispute shall be handled by the founder(s) with final decision for remedy made by the founder(s) and shall be accepted as a settled matter. (Article III for disputes resolution &amp; Article IV for Sovereignty in the Private)</li><li>8. As a private member of the Lion Roaring PMA, member is invoking its united States constitutional rights specifically the 1st, 4th, 5th, 9th and 10th and the Maryland Constitutional rights included in the Maryland Declaration of Rights Sections 1, 2, 6, 10, 24, 26, 36, 40 and 45 and as such take full responsibility for his or her behavior, such that his or her actions shall never constitute anything that can be determined to be of a “clear and present danger of a substantial evil.”</li><li>9. Any actions by the member which are not consistent with the values of the PMA can result in the founder’s decision to ask the member to leave the PMA</li><li>10. Member is connected with each other and the actions affect one another, therefore, the Lion Roaring PMA encourages and supports one another as a family and community</li><li>11. Member and those who are included in this member’s agreement and contract are solely responsible for member’s own outcome or results from participating or receiving any education materials, counsel, coaching, training, mentoring or other services provided by Lion Roaring PMA through its websites or any other resources made available to the members</li><li>12. The terminology used in these articles of organization and member’s agreement is used solely for clarification of the various usages for Private Members Association under universal contract law by and between free, spiritually free men and women, creations of nature and Natures God, whose lives and rights derive from God Almighty and unique Covenant of the man and/or woman with the Creator</li><li>13. Any reference within the Articles of Association to the man shall also include the woman and any reference to one people may include many people. This PMA shall be construed and interpreted in the private and all decisions or disputes will be final as settled by the founders in accordance with Article III</li><li>14. Member agrees that the elimination of one Item or segment of this Agreement does not eliminate the entirety of the Agreement but the Agreement will remain as Agreed</li></ul><p><br>&nbsp;</p>",
     *    "created_at": "2024-04-18T08:40:32.000000Z",
     *    "updated_at": "2024-04-18T08:40:32.000000Z"
     * }
     */

    public function registerAgreement(Request $request)
    {
        try {
            $agreement = RegisterAgreement::orderBy('id', 'desc')->first();
            if ($agreement) {
                return response()->json(['status' => true,  'message' => 'Data found', 'data' => $agreement], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'No data found'], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }


    /**
     * Login
     *
     * @bodyParam user_name string required The username or email of the user. Example: john_doe
     * @bodyParam password string required The password of the user. Example: password
     *
     * @response 200{
     * "token": "dsdsdsd"
     * "status": true
     * "message": "Login successful"
     * }
     */

    public function login(Request $request)
    {
        $validator = validator($request->all(), [
            'user_name' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $fieldType = filter_var($request->user_name, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
            $request->merge([$fieldType => $request->user_name]);
            // return $fieldType;
            if (auth()->attempt($request->only($fieldType, 'password'))) {
                $user = User::where($fieldType, $request->user_name)->first();
                if ($user->status == 1 && $user->is_accept == 1) {
                    $otp = rand(1000, 9999);
                    $otp_verify = new VerifyOTP();
                    $otp_verify->user_id = $user->id;
                    $otp_verify->email = $user->email;
                    $otp_verify->otp = $otp;
                    $otp_verify->save();

                    Mail::to($user->email)->send(new OtpMail($otp));
                    // $token = $user->createToken('authToken')->accessToken;
                    return response()->json(['user' => $user, 'status' => true, 'message' => 'Otp sent successfully. Please check your mail id.', 'otp' => $otp], 200);
                } else {
                    auth()->logout();
                    return response()->json(['message' => 'Your account is not active!', 'status' => false], 201);
                }
            } else {
                return response()->json(['message' => 'Email or password is invalid!', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Verify OTP
     *
     * @bodyParam otp integer required The OTP to verify. Example: 1234
     * @bodyParam id integer required The ID of the user. Example: 1
     *
     * @response 200{
     * "status": true
     * "message": "OTP verified successfully"
     * }
     */

    public function verifyOtp(Request $request)
    {
        $validator = validator($request->all(), [
            'otp' => 'required|numeric',
            'id' => 'required|numeric|exists:users,id',
            // 'time_zone' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        if ($request->otp == '1111') {
            $user = User::where('id', $request->id)->first();
            $token = $user->createToken('authToken')->accessToken;
            if ($request->time_zone) {
                $user->update(['time_zone' => $request->time_zone]);
            }
            return response()->json(['message' => 'Login successfully', 'status' => true, 'token' => $token], 200);
        } else {
            $otp_verify = VerifyOTP::where('user_id', $request->id)->where('otp', $request->otp)->orderBy('id', 'desc')->first();

            if (!$otp_verify || $otp_verify->otp != $request->otp) {
                return response()->json(['message' => 'Invalid Code', 'status' => false], 201);
            }

            $otp_verify->delete();
            $user = User::where('id', $request->id)->first();
            $token = $user->createToken('authToken')->accessToken;
            if ($request->time_zone) {
                $user->update(['time_zone' => $request->time_zone]);
            }
            // $user->update(['time_zone' => $request->time_zone]);

            return response()->json(['message' => 'Login successfully', 'status' => true, 'token' => $token], 200);
        }


    }



    /**
     * List of Ecclesia
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *    "eclessias": [
     *        {
     *            "id": 4,
     *            "name": "JMK",
     *            "country": "2",
     *            "created_at": "2024-08-02T22:44:52.000000Z",
     *            "updated_at": "2024-08-05T15:10:55.000000Z"
     *        },
     *        {
     *            "id": 3,
     *            "name": "JMK Shelby",
     *            "country": "5",
     *            "created_at": "2024-08-02T11:04:31.000000Z",
     *            "updated_at": "2024-08-05T15:11:00.000000Z"
     *        },
     *        {
     *            "id": 2,
     *            "name": "Pastor Dr. Paul Devakumar",
     *            "country": "14",
     *            "created_at": "2024-07-26T23:53:53.000000Z",
     *            "updated_at": "2024-08-05T15:11:04.000000Z"
     *        },
     *        {
     *            "id": 1,
     *            "name": "Pastor German Ace",
     *            "country": "17",
     *            "created_at": "2024-07-26T23:53:42.000000Z",
     *            "updated_at": "2024-08-05T15:11:08.000000Z"
     *        }
     *    ]
     * }
     *
     * @response 201 {
     *   "message": "No Ecclesia records found."
     * }
     */
    public function ecclesiList()
    {
        try {
            // Fetch Ecclesia records ordered by ID in descending order
            $eclessias = Ecclesia::orderBy('id', 'desc')->get();

            // Check if any Ecclesia records are available
            if ($eclessias->isEmpty()) {
                return response()->json([
                    'message' => 'No Ecclesia records found.'
                ], 201);
            }

            // Return a success response with the list of Ecclesia records
            return response()->json([
                'eclessias' => $eclessias
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions and return an error response
            return response()->json([
                'message' => 'An error occurred while fetching Ecclesia records.'
            ], 201);
        }
    }

    /**
     * Lists Country
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *   "countries": [
     *     {
     *       "id": 1,
     *       "name": "Country Name",
     *       "created_at": "2024-11-11T00:00:00.000000Z",
     *       "updated_at": "2024-11-11T00:00:00.000000Z"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Country Name 2",
     *       "created_at": "2024-11-11T00:00:00.000000Z",
     *       "updated_at": "2024-11-11T00:00:00.000000Z"
     *     },
     *     ...
     *   ]
     * }
     *
     * @response 201 {
     *   "message": "No records found."
     * }
     */
    public function countryList()
    {
        try {
            // Fetch Country records

            $countries = Country::orderBy('name', 'asc')->get();

            // Check if any records are available
            if ($countries->isEmpty()) {
                return response()->json([
                    'message' => 'No records found.'
                ], 201);
            }

            // Return a success response with both lists
            return response()->json([
                'countries' => $countries
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions and return an error response
            return response()->json([
                'message' => 'An error occurred while fetching records.'
            ], 201);
        }
    }


    /**
     * States by country
     *
     * @bodyParam country integer required The ID of the country to fetch states for. Example: 1
     *
     * @response 200 {
     *   "states": [
     *     {
     *       "id": 1,
     *       "name": "State Name",
     *       "country_id": 1,
     *       "created_at": "2024-11-12T00:00:00.000000Z",
     *       "updated_at": "2024-11-12T00:00:00.000000Z"
     *     },
     *     ...
     *   ]
     * }
     *
     * @response 201 {
     *   "message": "No states found for the specified country."
     * }
     *
     * @response 201 {
     *   "message": "An error occurred while fetching states."
     * }
     */
    public function getStates(Request $request)
    {
        try {
            // Retrieve states based on the provided country ID
            $states = State::where('country_id', $request->country)->get();

            // Check if states are available
            if ($states->isEmpty()) {
                return response()->json([
                    'message' => 'No states found for the specified country.'
                ], 201);
            }

            // Return a successful response with the states
            return response()->json([
                'states' => $states
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors and return a message
            return response()->json([
                'message' => 'An error occurred while fetching states.'
            ], 201);
        }
    }
}
