<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @group E-Store Addresses
 */
class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        $addresses = UserAddress::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Addresses.',
            'data' => $addresses,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        if ($data instanceof JsonResponse) {
            return $data;
        }

        $address = DB::transaction(function () use ($data) {
            if (!empty($data['is_default'])) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            }
            $data['user_id'] = auth()->id();
            return UserAddress::create($data);
        });

        return response()->json([
            'status' => true,
            'message' => 'Address created.',
            'data' => $address,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        return response()->json(['status' => true, 'message' => 'Address.', 'data' => $address]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        $data = $this->validated($request);
        if ($data instanceof JsonResponse) {
            return $data;
        }

        DB::transaction(function () use ($data, $address) {
            if (!empty($data['is_default'])) {
                UserAddress::where('user_id', auth()->id())
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
            $address->update($data);
        });

        return response()->json([
            'status' => true,
            'message' => 'Address updated.',
            'data' => $address->fresh(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        $address->delete();

        return response()->json(['status' => true, 'message' => 'Address deleted.']);
    }

    /**
     * @return array|JsonResponse  Returns validated data, or a 422 JsonResponse on failure.
     */
    private function validated(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:50',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'country_code' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'formatted_address' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        return $validator->validated();
    }
}
