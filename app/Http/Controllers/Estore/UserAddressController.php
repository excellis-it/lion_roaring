<?php

namespace App\Http\Controllers\Estore;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $addresses = UserAddress::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'label' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:120'],
            'country_code' => ['nullable', 'string', 'max:5'],
            'formatted_address' => ['nullable', 'string', 'max:1000'],
            'make_default' => ['nullable', 'boolean'],
        ]);

        // Guest users: preserve current behavior via session (warehouse selection etc.)
        if (!auth()->check()) {
            session([
                'location_lat' => (float) $validated['latitude'],
                'location_lng' => (float) $validated['longitude'],
                'location_address' => $validated['formatted_address'] ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Location saved',
            ]);
        }

        $makeDefault = (bool) ($validated['make_default'] ?? true);

        $address = DB::transaction(function () use ($validated, $makeDefault) {
            if ($makeDefault) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            }

            $address = UserAddress::create([
                'user_id' => auth()->id(),
                'label' => $validated['label'] ?? null,
                'address_line1' => $validated['address_line1'] ?? null,
                'address_line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => $validated['state'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'country' => $validated['country'] ?? null,
                'country_code' => $validated['country_code'] ?? 'US',
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
                'formatted_address' => $validated['formatted_address'] ?? null,
                'is_default' => $makeDefault,
            ]);

            if ($makeDefault) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $user->location_lat = (float) $validated['latitude'];
                $user->location_lng = (float) $validated['longitude'];
                $user->location_address = $validated['formatted_address'] ?? null;
                $user->location_zip = $validated['postal_code'] ?? null;
                $user->location_country = $validated['country'] ?? null;
                $user->location_state = $validated['state'] ?? null;
                $user->save();
            }

            return $address;
        });

        return response()->json([
            'status' => true,
            'message' => 'Address saved',
            'data' => $address,
        ]);
    }

    public function setDefault(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $validated = $request->validate([
            'address_id' => ['required', 'integer'],
        ]);

        $address = UserAddress::where('user_id', auth()->id())
            ->where('id', $validated['address_id'])
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }

        DB::transaction(function () use ($address) {
            UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            $address->is_default = true;
            $address->save();

            /** @var \App\Models\User $user */
            $user = auth()->user();
            $user->location_lat = $address->latitude;
            $user->location_lng = $address->longitude;
            $user->location_address = $address->formatted_address;
            $user->location_zip = $address->postal_code;
            $user->location_country = $address->country;
            $user->location_state = $address->state;
            $user->save();
        });

        return response()->json([
            'status' => true,
            'message' => 'Default address updated',
        ]);
    }

    public function update(Request $request, UserAddress $address)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if ((int) $address->user_id !== (int) auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found',
            ], 404);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'label' => ['nullable', 'string', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'country' => ['nullable', 'string', 'max:120'],
            'country_code' => ['nullable', 'string', 'max:5'],
            'formatted_address' => ['nullable', 'string', 'max:1000'],
            'make_default' => ['nullable', 'boolean'],
        ]);

        $makeDefault = (bool) ($validated['make_default'] ?? $address->is_default);

        $address = DB::transaction(function () use ($address, $validated, $makeDefault) {
            if ($makeDefault) {
                UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            }

            $address->label = $validated['label'] ?? null;
            $address->address_line1 = $validated['address_line1'] ?? null;
            $address->address_line2 = $validated['address_line2'] ?? null;
            $address->city = $validated['city'] ?? null;
            $address->state = $validated['state'] ?? null;
            $address->postal_code = $validated['postal_code'] ?? null;
            $address->country = $validated['country'] ?? null;
            $address->country_code = $validated['country_code'] ?? ($address->country_code ?? 'US');
            $address->latitude = (float) $validated['latitude'];
            $address->longitude = (float) $validated['longitude'];
            $address->formatted_address = $validated['formatted_address'] ?? null;
            $address->is_default = $makeDefault;
            $address->save();

            if ($makeDefault) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $user->location_lat = $address->latitude;
                $user->location_lng = $address->longitude;
                $user->location_address = $address->formatted_address;
                $user->location_zip = $address->postal_code;
                $user->location_country = $address->country;
                $user->location_state = $address->state;
                $user->save();
            }

            return $address;
        });

        return response()->json([
            'status' => true,
            'message' => 'Address updated',
            'data' => $address,
        ]);
    }
}
