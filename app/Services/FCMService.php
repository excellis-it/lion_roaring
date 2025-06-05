<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;


class FCMService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('app/public/firebase-adminsdk.json'));

            $this->messaging = $factory->createMessaging();
        } catch (Exception $e) {
            Log::error('FCM Service initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send notification to a single device
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);


            $androidConfig = AndroidConfig::fromArray([
                // 'ttl' => '3600s',
                'priority' => 'high',
                'notification' => [
                    // 'title' => '$GOOG up 1.43% on the day',
                    // 'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                    // 'icon' => 'stock_ticker_update',
                    // 'color' => '#f45342',
                    'sound' => 'default',
                    'visibility' => 'public',
                    'channel_id' => 'high_importance_channel',
                ],
            ]);

            // apns config
            $apnsConfig = ApnsConfig::fromArray([
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'sound' => 'default', // <-- move here
                        'content-available' => 1,
                        'mutable-content' => 1,
                    ],
                ],
            ]);

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data)
                ->withAndroidConfig($androidConfig)
                ->withApnsConfig($apnsConfig);

            $result = $this->messaging->send($message);

            Log::info('FCM notification sent successfully', [
                'token' => $token,
                'title' => $title,
                'result' => $result
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('FCM notification failed', [
                'token' => $token,
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $result = $this->messaging->sendMulticast($message, $tokens);

            Log::info('FCM multicast notification sent', [
                'tokens_count' => count($tokens),
                'title' => $title,
                'success_count' => $result->successes()->count(),
                'failure_count' => $result->failures()->count()
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('FCM multicast notification failed', [
                'tokens_count' => count($tokens),
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send data-only message (silent notification)
     */
    public function sendDataMessage(string $token, array $data)
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withData($data);

            $result = $this->messaging->send($message);

            Log::info('FCM data message sent successfully', [
                'token' => $token,
                'data' => $data,
                'result' => $result
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('FCM data message failed', [
                'token' => $token,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate FCM token
     */
    public function validateToken(string $token): bool
    {
        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withData(['test' => 'validation']);

            $this->messaging->validate($message);
            return true;
        } catch (Exception $e) {
            Log::warning('FCM token validation failed', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
