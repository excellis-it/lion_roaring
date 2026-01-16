<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SignupRule;

class SignupRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing rules
        SignupRule::truncate();

        // Example Rule 1: Email must be from specific domains (CRITICAL - affects user status)
        SignupRule::create([
            'field_name' => 'email',
            'rule_type' => 'email_domain',
            'rule_value' => 'lionroaring.us, lionroaring.org', // Allowed domains
            'error_message' => 'Email must be from an approved domain (gmail.com, yahoo.com, outlook.com, or hotmail.com)',
            'description' => 'Only allow emails from trusted domains',
            'is_active' => true,
            'is_critical' => true, // If this fails, user becomes inactive
            'priority' => 10,
        ]);

        // Example Rule 2: Phone number must be exactly 10 digits (CRITICAL)
        SignupRule::create([
            'field_name' => 'phone_number',
            'rule_type' => 'phone_length',
            'rule_value' => '10', // Exact length
            'error_message' => 'Phone number must be exactly 10 digits',
            'description' => 'Validate phone number length',
            'is_active' => true,
            'is_critical' => true, // If this fails, user becomes inactive
            'priority' => 9,
        ]);

        
    }
}
