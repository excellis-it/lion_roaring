<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
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
}
