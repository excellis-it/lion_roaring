<?php

namespace Tests\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesApiUsers
{
    protected function createApiUser(array $overrides = []): User
    {
        $unique = Str::lower(Str::random(8));

        $user = User::create(array_merge([
            'user_name' => 'test_' . $unique,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test_' . $unique . '@api-contract.test',
            'phone' => '555' . random_int(1000000, 9999999),
            'password' => Hash::make('Password@123'),
            'address' => '123 Test St',
            'city' => 'Testville',
            'zip' => '12345',
        ], $overrides));

        $user->status = $overrides['status'] ?? 1;
        $user->is_accept = $overrides['is_accept'] ?? 1;
        $user->save();

        return $user->fresh();
    }

    protected function bearerTokenFor(User $user): string
    {
        return $user->createToken('authToken')->accessToken;
    }
}
