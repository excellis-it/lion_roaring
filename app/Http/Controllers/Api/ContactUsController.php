<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\ContactUsCms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Contact Us
 */

class ContactUsController extends Controller
{
    protected $successStatus = 200;

    /**
     * Store Contact Us
     * @bodyParam first_name string required First Name of the user. Example: John
     * @bodyParam last_name string required Last Name of the user. Example: Doe
     * @bodyParam email string required Email of the user. Example:
     * @bodyParam phone string required Phone of the user. Example: 1234567890
     * @bodyParam message string required Message of the user. Example: Hello
     * @response {
     * "message": "Thank you for contacting us",
     * "status": true
     * }
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'status' => false], 201);
        }

        try {
            $contact = new ContactUs();
            $contact->first_name = $request->first_name;
            $contact->last_name = $request->last_name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->message = $request->message;
            $contact->save();

            return response()->json(['message' => 'Thank you for contacting us', 'status' => true], $this->successStatus);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 401);
        }
    }


    /**
     * Contact us page
     * 
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "address": "123 Main St, Springfield, USA",
     *     "phone": "+123456789",
     *     "email": "info@example.com",
     *     "created_at": "2024-12-09T10:00:00.000000Z",
     *     "updated_at": "2024-12-09T10:00:00.000000Z"
     *   }
     * }
     * @response 201 {
     *   "message": "Failed to retrieve contact us information.",
     *   "error": "Database error or contact information not found."
     * }
     */
    public function contactUs()
    {
        try {
            $contact = ContactUsCms::first();

            if (!$contact) {
                return response()->json(['message' => 'Contact information not found.'], 201);
            }

            return response()->json(['data' => $contact], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve contact us information.',
                'error' => $e->getMessage(),
            ], 201);
        }
    }
}
