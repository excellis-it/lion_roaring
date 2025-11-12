<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Helpers\helper;
use App\Models\User;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'email',
        'user_roles',
        'ecclesia_name',
        'ip',
        'country_code',
        'country_name',
        'device_mac',
        'device_type',
        'browser',
        'url',
        'permission_access',
        'activity_type',
        'activity_description',
        'activity_date',
    ];

    /**
     * Reusable static function to log activity manually
     */
    public static function logActivity($data = [])
    {
        try {
            $user = auth()->user();
            $ip = request()->ip();
            $countryCode = helper::getVisitorCountryCode(); // existing helper
            $countryName = helper::getVisitorCountryName();

            $userRoles = $user ? implode(',', $user->getRoleNames()->toArray()) : null;

            $activityType =  strtoupper(str_replace('/', '_', trim(request()->path(), '/')));

            // if data provided user id then use it by user model otherwise use defaults
            $userData = [];
            if (isset($data['user_id'])) {
                $userModel = User::find($data['user_id']);
                if ($userModel) {
                    $userData['user_id'] = $userModel->id;
                    $userData['user_name'] = $userModel->first_name . ' ' . $userModel->last_name;
                    $userData['email'] = $userModel->email;
                    $userData['user_roles'] = implode(',', $userModel->getRoleNames()->toArray());
                }
            }

            self::create([
                'user_id'            => $userData['user_id'] ?? $user?->id ?? null,
                'user_name'          => $userData['user_name'] ?? ($user ? ($user->first_name . ' ' . $user->last_name) : 'Guest'),
                'email'              =>  $userData['email'] ?? $user?->email ?? '-',
                'user_roles'        => $userData['user_roles'] ?? $userRoles ?? '-',
                'ecclesia_name'      => optional($user?->ecclesia)->name ?? '-',
                'ip'                 => $ip ?? '-',
                'country_code'       => $countryCode,
                'country_name'       => $countryName,
                'device_mac'         => $data['device_mac'] ?? null,
                'device_type'        => request()->header('User-Agent'),
                'browser'            => self::getBrowserName(),
                'url'                => url()->current(),
                'permission_access'  => $data['permission_access'] ?? null,
                'activity_type'      => $activityType ?? '-',
                'activity_description' => $data['activity_description'] ?? 'Visited a page',
                'activity_date'      => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Activity log failed: ' . $e->getMessage());
        }
    }

    private static function getBrowserName()
    {
        $ua = request()->header('User-Agent');
        if (strpos($ua, 'Firefox') !== false) return 'Firefox';
        if (strpos($ua, 'Chrome') !== false) return 'Chrome';
        if (strpos($ua, 'Safari') !== false) return 'Safari';
        if (strpos($ua, 'Edge') !== false) return 'Edge';
        if (strpos($ua, 'Opera') !== false) return 'Opera';
        return 'Unknown';
    }
}
