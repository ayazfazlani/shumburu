<?php

namespace App\Notifications\Channels;

use App\Models\PushSubscription;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $subject = config('services.webpush.subject');
        $publicKey = config('services.webpush.public_key');
        $privateKey = config('services.webpush.private_key');

        if (! $subject || ! $publicKey || ! $privateKey) {
            Log::warning('Web Push skipped because VAPID credentials are not configured.');
            return;
        }

        $data = $notification->toArray($notifiable);
        $payload = json_encode([
            'title' => $data['title'] ?? 'New notification',
            'body' => $data['message'] ?? '',
            'url' => $data['action_url'] ?? url('/notifications'),
            'icon' => asset('favicon.ico'),
        ], JSON_THROW_ON_ERROR);

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);

        foreach ($notifiable->pushSubscriptions as $storedSubscription) {
            $subscription = Subscription::create([
                'endpoint' => $storedSubscription->endpoint,
                'publicKey' => $storedSubscription->public_key,
                'authToken' => $storedSubscription->auth_token,
                'contentEncoding' => $storedSubscription->content_encoding,
            ]);

            try {
                $report = $webPush->sendOneNotification($subscription, $payload, ['TTL' => 3600]);

                if ($report->isSubscriptionExpired()) {
                    $storedSubscription->delete();
                } elseif (! $report->isSuccess()) {
                    Log::warning('Web Push delivery failed.', [
                        'user_id' => $notifiable->getKey(),
                        'reason' => $report->getReason(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::warning('Web Push delivery threw an exception.', [
                    'user_id' => $notifiable->getKey(),
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
