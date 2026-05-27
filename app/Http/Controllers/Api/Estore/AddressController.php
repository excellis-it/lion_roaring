<?php

namespace App\Http\Controllers\Api\Estore;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            $hasAddresses = UserAddress::where('user_id', auth()->id())->exists();
            if (!$hasAddresses) {
                $data['is_default'] = true;
            }
            if (!empty($data['is_default'])) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            }
            $data['user_id'] = auth()->id();
            $created = UserAddress::create($data);

            if (!empty($data['is_default'])) {
                $this->syncUserLocationFromAddress($created);
            }

            return $created;
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

            if (!empty($data['is_default'])) {
                $this->syncUserLocationFromAddress($address->fresh());
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Address updated.',
            'data' => $address->fresh(),
        ]);
    }

    public function setDefault(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address_id' => ['required', 'integer'],
        ]);

        $address = UserAddress::where('user_id', auth()->id())
            ->where('id', $validated['address_id'])
            ->first();

        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        DB::transaction(function () use ($address) {
            UserAddress::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
            $address->is_default = true;
            $address->save();
            $this->syncUserLocationFromAddress($address);
        });

        return response()->json([
            'status' => true,
            'message' => 'Default address updated',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', auth()->id())->find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        $wasDefault = (bool) $address->is_default;

        DB::transaction(function () use ($address, $wasDefault) {
            $address->delete();

            if (!$wasDefault) {
                return;
            }

            $replacement = UserAddress::query()
                ->where('user_id', auth()->id())
                ->orderByDesc('id')
                ->first();

            /** @var User $user */
            $user = auth()->user();

            if ($replacement) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
                $replacement->is_default = true;
                $replacement->save();
                $this->syncUserLocationFromAddress($replacement);

                return;
            }

            $user->location_lat = null;
            $user->location_lng = null;
            $user->location_address = null;
            $user->location_zip = null;
            $user->location_country = null;
            $user->location_state = null;
            $user->save();
        });

        return response()->json(['status' => true, 'message' => 'Address deleted.']);
    }

    private function syncUserLocationFromAddress(UserAddress $address): void
    {
        if ($address->latitude === null || $address->longitude === null) {
            return;
        }

        $formatted = trim((string) ($address->formatted_address ?? ''));
        if ($formatted === '') {
            $formatted = collect([
                $address->address_line1,
                $address->address_line2,
                $address->city,
                $address->state,
                $address->postal_code,
                $address->country,
            ])->filter(fn ($part) => filled($part))->implode(', ');
        }

        /** @var User $user */
        $user = auth()->user();
        $user->location_lat = $address->latitude;
        $user->location_lng = $address->longitude;
        $user->location_address = $formatted !== '' ? $formatted : null;
        $user->location_zip = $address->postal_code;
        $user->location_country = $address->country;
        $user->location_state = $address->state;
        $user->save();
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
