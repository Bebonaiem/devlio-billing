<?php
namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use NotificationPusher\Models\PushSubscription;

class WebPushService
{
    public function subscribe(User $user, string $endpoint, string $key, string $authSecret): bool
    {
        try {
            $subscription = PushSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $endpoint,
                ],
                [
                    'public_key' => $key,
                    'auth_secret' => $authSecret,
                ]
            );

            return $subscription->exists;

        } catch (\Exception $e) {
            Log::error('Failed to subscribe push notification: '.$e->getMessage());

            return false;
        }
    }

    public function unsubscribe(User $user, string $endpoint): bool
    {
        return PushSubscription::where('user_id', $user->id)
            ->where('endpoint', $endpoint)
            ->delete() > 0;
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $preferences = NotificationPreference::where('user_id', $user->id)
            ->where('channel', 'web_push')
            ->first();

        if (! $preferences || ! $preferences->enabled) {
            return;
        }

        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $subscription) {
            $this->sendNotification($subscription, $title, $body, $data);
        }
    }

    public function sendNotification(PushSubscription $subscription, string $title, string $body, array $data = []): void
    {
        try {
            $payload = [
                'title' => $title,
                'body' => $body,
                'icon' => '/images/notification-icon.png',
                'badge' => '/images/badge-icon.png',
                'data' => $data,
                'actions' => $data['actions'] ?? [],
            ];

            // Using minishlink/web-push or similar library
            // This is a placeholder - implement with your preferred web push library
            Log::info("Web push notification sent to user {$subscription->user_id}", $payload);

        } catch (\Exception $e) {
            Log::error('Failed to send web push notification: '.$e->getMessage());
        }
    }

    public function sendBulkNotifications(array $userIds, string $title, string $body, array $data = []): void
    {
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $this->sendToUser($user, $title, $body, $data);
        }
    }

    public function sendToAllSubscribers(string $title, string $body, array $data = []): void
    {
        $subscriptions = PushSubscription::all();

        foreach ($subscriptions as $subscription) {
            $this->sendNotification($subscription, $title, $body, $data);
        }
    }
}
