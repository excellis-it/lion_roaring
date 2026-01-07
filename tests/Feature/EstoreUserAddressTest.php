<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstoreUserAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_address_and_set_default()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $resp1 = $this->post(route('e-store.addresses.store'), [
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'formatted_address' => 'Dhaka, Bangladesh',
            'city' => 'Dhaka',
            'state' => 'Dhaka',
            'country' => 'Bangladesh',
            'postal_code' => '1000',
            'label' => 'Home',
            'make_default' => 1,
        ]);

        $resp1->assertStatus(200)->assertJson(['status' => true]);

        $this->assertDatabaseCount('user_addresses', 1);

        $addr1 = UserAddress::first();
        $this->assertTrue((bool) $addr1->is_default);

        $user->refresh();
        $this->assertEquals(23.8103, round((float) $user->location_lat, 4));
        $this->assertEquals(90.4125, round((float) $user->location_lng, 4));

        $resp2 = $this->post(route('e-store.addresses.store'), [
            'latitude' => 24.8949,
            'longitude' => 91.8687,
            'formatted_address' => 'Sylhet, Bangladesh',
            'city' => 'Sylhet',
            'state' => 'Sylhet',
            'country' => 'Bangladesh',
            'postal_code' => '3100',
            'label' => 'Office',
            'make_default' => 0,
        ]);

        $resp2->assertStatus(200)->assertJson(['status' => true]);
        $this->assertDatabaseCount('user_addresses', 2);

        $addr2 = UserAddress::orderByDesc('id')->first();
        $this->assertFalse((bool) $addr2->is_default);

        $resp3 = $this->post(route('e-store.addresses.default'), [
            'address_id' => $addr2->id,
        ]);

        $resp3->assertStatus(200)->assertJson(['status' => true]);

        $addr1->refresh();
        $addr2->refresh();
        $this->assertFalse((bool) $addr1->is_default);
        $this->assertTrue((bool) $addr2->is_default);

        $user->refresh();
        $this->assertEquals(24.8949, round((float) $user->location_lat, 4));
        $this->assertEquals(91.8687, round((float) $user->location_lng, 4));
    }
}
