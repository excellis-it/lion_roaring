<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegisterAgreement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *  @group Authentication
 */
class AuthController extends Controller
{
    protected $successStatus = 200;

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
                if ($user->status == 1) {
                    $token = $user->createToken('authToken')->accessToken;
                    return response()->json(['token' => $token, 'status' => true, 'message' => 'Login successful'], 200);
                } else {
                    auth()->logout();
                    return response()->json(['message' => 'Your account is not active!', 'status' => false], 201);
                }
            } else {
                return response()->json(['message' => 'Unauthorised', 'status' => false], 201);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }

    /**
     * Register
     *
     * @bodyParam user_name string required The username of the user. Example: john_doe
     * @bodyParam email string required The email of the user. Example:
     * @bodyParam first_name string required The first name of the user. Example: John
     * @bodyParam last_name string required The last name of the user. Example: Doe
     * @bodyParam middle_name string The middle name of the user. Example: Doe
     * @bodyParam address string required The address of the user. Example: 123, New York
     * @bodyParam phone_number numeric The phone number of the user. Example: 1234567890
     * @bodyParam email_confirmation string required The email confirmation of the user. Example:
     * @bodyParam password string required The password of the user. Example: password
     * @bodyParam password_confirmation string required The password confirmation of the user. Example: password
     *
     * @response 200{
     * "token": "dsdsdsd"
     * "status": true
     * "message": "Registration successful"
     * }
     *
     * @response 201{
     * "message": "The user name has already been taken."
     * "status": false
     * }
     */

    public function register(Request $request)
    {
        $validator = validator($request->all(), [
            'user_name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'nullable|numeric',
            'email_confirmation' => 'required|same:email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $user = new User();
            $user->user_name = $request->user_name;
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->middle_name = $request->middle_name;
            $user->address = $request->address;
            $user->phone = $request->phone_number;
            $user->password = bcrypt($request->password);
            $user->status = 1;
            $user->save();
            $user->assignRole('CUSTOMER');
            $token = $user->createToken('authToken')->accessToken;
            return response()->json(['token' => $token, 'status' => true, 'message' => 'Registration successful'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
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
}
