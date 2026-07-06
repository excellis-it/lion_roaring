<?php

namespace App\Http\Controllers\Concerns;

use App\Mail\SendUserNameMail;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait SendsUsernameRecoveryEmails
{
    protected function findUsersByPhone(?string $phoneNumber): Collection
    {
        if (!$phoneNumber) {
            return collect();
        }

        return User::matchingPhone($phoneNumber)
            ->where('is_accept', 1)
            ->orderBy('id')
            ->get(['id', 'email', 'user_name', 'phone', 'is_accept']);
    }

    /**
     * @return array<int, string> Masked email addresses that received recovery mail.
     */
    protected function sendUsernameRecoveryEmails(Collection $users): array
    {
        $maskedEmails = [];

        foreach ($users as $user) {
            PasswordReset::where('email', $user->email)->delete();
            $token = Str::random(20) . 'pass' . $user->id;
            PasswordReset::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $details = [
                'id' => Crypt::encrypt($user->id),
                'token' => $token,
            ];

            $recipientEmail = $user->email;
            $username = $user->user_name;

            Mail::to($recipientEmail)->send(new SendUserNameMail([
                'user_name' => $username,
                'email' => $recipientEmail,
            ], $details));

            $maskedEmails[] = $this->maskEmailForDisplay($recipientEmail);
        }

        return $maskedEmails;
    }

    protected function maskEmailForDisplay(string $email): string
    {
        $at = strpos($email, '@');
        if ($at === false) {
            return '****';
        }

        $localPart = substr($email, 0, $at);
        $domain = substr($email, $at + 1);

        if (strlen($localPart) <= 6) {
            $maskedLocal = substr($localPart, 0, 1) . '****';
        } elseif (strlen($localPart) <= 8) {
            $maskedLocal = substr($localPart, 0, 3) . '****' . substr($localPart, -1);
        } else {
            $maskedLocal = substr($localPart, 0, 4) . '****' . substr($localPart, -2);
        }

        return $maskedLocal . '@****' . $domain;
    }
}
