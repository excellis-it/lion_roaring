<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SignupRule;

class SignupRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rule 1: Email domain restriction
        SignupRule::firstOrCreate(
            [
                'field_name' => 'email',
                'rule_type'  => 'email_domain',
            ],
            [
                'rule_value'    => 'lionroaring.us,lionroaring.org',
                'error_message' => 'Email must be from an approved domain',
                'description'   => 'Only allow emails from trusted domains',
                'is_active'     => true,
                'is_critical'   => true,
                'priority'      => 10,
            ]
        );

        // Rule 2: Phone number length
        SignupRule::firstOrCreate(
            [
                'field_name' => 'phone_number',
                'rule_type'  => 'phone_length',
            ],
            [
                'rule_value'    => '10',
                'error_message' => 'Phone number must be exactly 10 digits',
                'description'   => 'Validate phone number length',
                'is_active'     => true,
                'is_critical'   => true,
                'priority'      => 9,
            ]
        );
    }
}
