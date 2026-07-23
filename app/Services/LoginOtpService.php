<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use App\Models\VerifyOTP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

final class LoginOtpService
{
    private const COOLDOWN_SECONDS = 60;

    public function issue(User $user): int
    {
        $this->otpLog('OTP_FLOW_START', [
            'user_id' => $user->id,
            'email' => $user->email,
        ], 'info');

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
                $this->otpLog('OTP_MAIL_SKIPPED_COOLDOWN_NO_MAIL_SENT', [
                    'user_id' => $lockedUser->id,
                    'email' => $lockedUser->email,
                    'otp_id' => $recentOtp->id,
                    'created_at' => optional($recentOtp->created_at)->toDateTimeString(),
                    'cooldown_seconds' => self::COOLDOWN_SECONDS,
                ], 'warning');

                return (int) $recentOtp->otp;
            }

            $otpRecord = new VerifyOTP();
            $otpRecord->user_id = $lockedUser->id;
            $otpRecord->email = $lockedUser->email;
            $otpRecord->otp = random_int(1000, 9999);
            $otpRecord->save();

            $mailConfig = [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from' => config('mail.from.address'),
                'username_set' => filled(config('mail.mailers.smtp.username')),
            ];

            $this->otpLog('OTP_MAIL_HIT_SENDING_NOW', [
                'user_id' => $lockedUser->id,
                'email' => $lockedUser->email,
                'otp_id' => $otpRecord->id,
                'mail' => $mailConfig,
            ], 'info');

            try {
                Mail::to($lockedUser->email)->send(new OtpMail($otpRecord->otp));
            } catch (Throwable $e) {
                $this->otpLog('OTP_MAIL_FAILED', [
                    'user_id' => $lockedUser->id,
                    'email' => $lockedUser->email,
                    'otp_id' => $otpRecord->id,
                    'mail' => $mailConfig,
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ], 'error');

                throw new RuntimeException(
                    'Failed to send login OTP email: ' . $e->getMessage(),
                    0,
                    $e
                );
            }

            $this->otpLog('OTP_MAIL_SENT_OK', [
                'user_id' => $lockedUser->id,
                'email' => $lockedUser->email,
                'otp_id' => $otpRecord->id,
                'mail' => $mailConfig,
            ], 'info');

            return (int) $otpRecord->otp;
        });
    }

    /**
     * Write login OTP events to laravel.log and storage/logs/otp.log.
     * Only real failures use error level.
     */
    private function otpLog(string $event, array $context = [], string $level = 'info'): void
    {
        $payload = array_merge(['event' => $event], $context);
        $message = '[LOGIN_OTP] ' . $event;

        Log::log($level, $message, $payload);

        try {
            Log::channel('otp')->log($level, $message, $payload);
        } catch (Throwable $e) {
            Log::error('[LOGIN_OTP] otp_channel_unavailable', [
                'event' => $event,
                'channel_error' => $e->getMessage(),
            ]);
        }
    }
}
