<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\VerifyOTP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

final class LoginOtpService
{
    private const COOLDOWN_SECONDS = 60;

    public function issue(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $lockedUser = User::whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $recentOtp = VerifyOTP::where('user_id', $lockedUser->id)
                ->latest('id')
                ->first();

            if (
                $recentOtp
                && $recentOtp->created_at
                && $recentOtp->created_at->gt(now()->subSeconds(self::COOLDOWN_SECONDS))
            ) {
                return (int) $recentOtp->otp;
            }

            $otpRecord = new VerifyOTP();
            $otpRecord->user_id = $lockedUser->id;
            $otpRecord->email = $lockedUser->email;
            $otpRecord->otp = random_int(1000, 9999);
            $otpRecord->save();

            Mail::to($lockedUser->email)->send(new OtpMail($otpRecord->otp));

            return (int) $otpRecord->otp;
        });
    }
}
