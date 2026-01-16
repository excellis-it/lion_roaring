<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignupRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_name',
        'rule_type',
        'rule_value',
        'error_message',
        'description',
        'is_active',
        'is_critical',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_critical' => 'boolean',
    ];

    /**
     * Validate signup data against all active rules
     *
     * @param array $data The signup form data
     * @return array ['passed' => bool, 'failed_rules' => array, 'user_should_be_active' => bool]
     */
    public static function validateSignupData($data)
    {
        $rules = self::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $failedRules = [];
        $criticalRuleFailed = false;

        foreach ($rules as $rule) {
            $fieldValue = $data[$rule->field_name] ?? null;
            $rulePassed = self::checkRule($rule, $fieldValue);

            if (!$rulePassed) {
                $failedRules[] = [
                    'field' => $rule->field_name,
                    'message' => $rule->error_message ?? "Field {$rule->field_name} does not meet requirements",
                    'is_critical' => $rule->is_critical,
                ];

                if ($rule->is_critical) {
                    $criticalRuleFailed = true;
                }
            }
        }

        return [
            'passed' => empty($failedRules),
            'failed_rules' => $failedRules,
            'user_should_be_active' => !$criticalRuleFailed, // Active if no critical rules failed
        ];
    }

    /**
     * Check if a single rule passes
     */
    private static function checkRule($rule, $value)
    {
        if (empty($value) && $rule->rule_type !== 'required') {
            return true; // Skip validation if field is empty and rule is not 'required'
        }

        switch ($rule->rule_type) {
            case 'required':
                return !empty($value);

            case 'regex':
                return preg_match($rule->rule_value, $value);

            case 'min_length':
                return strlen($value) >= intval($rule->rule_value);

            case 'max_length':
                return strlen($value) <= intval($rule->rule_value);

            case 'numeric':
                return is_numeric($value);

            case 'email_domain':
                // Check if email ends with specific domain
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $domain = substr(strrchr($value, "@"), 1);
                    $allowedDomains = explode(',', $rule->rule_value);
                    return in_array($domain, array_map('trim', $allowedDomains));
                }
                return false;

            case 'phone_length':
                // Remove non-numeric characters and check length
                $cleanPhone = preg_replace('/[^0-9]/', '', $value);
                return strlen($cleanPhone) == intval($rule->rule_value);

            case 'contains':
                // Check if value contains specific string
                return stripos($value, $rule->rule_value) !== false;

            case 'not_contains':
                // Check if value does NOT contain specific string
                return stripos($value, $rule->rule_value) === false;

            case 'min_value':
                return floatval($value) >= floatval($rule->rule_value);

            case 'max_value':
                return floatval($value) <= floatval($rule->rule_value);

            case 'in_list':
                // Check if value is in comma-separated list
                $allowedValues = explode(',', $rule->rule_value);
                return in_array($value, array_map('trim', $allowedValues));

            default:
                return true;
        }
    }

    /**
     * Get available rule types
     */
    public static function getRuleTypes()
    {
        return [
            'required' => 'Field is required',
            'regex' => 'Match regular expression pattern',
            'min_length' => 'Minimum character length',
            'max_length' => 'Maximum character length',
            'numeric' => 'Must be numeric',
            'email_domain' => 'Email must be from specific domain(s)',
            'phone_length' => 'Phone number must be exact length',
            'contains' => 'Must contain specific text',
            'not_contains' => 'Must NOT contain specific text',
            'min_value' => 'Minimum numeric value',
            'max_value' => 'Maximum numeric value',
            'in_list' => 'Must be one of the listed values',
        ];
    }

    /**
     * Get available fields for validation
     */
    public static function getAvailableFields()
    {
        return [
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'user_name' => 'Username',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'address' => 'Address',
            'address2' => 'Address Line 2',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'zip' => 'ZIP Code',
        ];
    }
}
