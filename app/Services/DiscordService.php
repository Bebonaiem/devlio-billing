<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    private ?string $botToken;

    private ?string $guildId;

    public function __construct()
    {
        $this->botToken = config('services.discord.bot_token');
        $this->guildId = config('services.discord.guild_id');
    }

    private function apiRequest(string $method, string $endpoint, array $data = []): ?array
    {
        if (! $this->botToken) {
            return null;
        }

        $url = 'https://discord.com/api/v10/'.ltrim($endpoint, '/');

        $response = Http::withHeaders([
            'Authorization' => 'Bot '.$this->botToken,
            'Content-Type' => 'application/json',
        ])->$method($url, $data);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Discord API request failed', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);

        return null;
    }

    public function sendMessage(string $channelId, string $message, ?array $embeds = null): ?array
    {
        $data = ['content' => $message];
        if ($embeds) {
            $data['embeds'] = $embeds;
        }

        return $this->apiRequest('post', "/channels/{$channelId}/messages", $data);
    }

    public function sendNotification(string $type, array $data): void
    {
        $channelId = config('services.discord.notification_channel');
        if (! $channelId) {
            return;
        }

        $message = match ($type) {
            'new_order' => "**New Order**\nUser: {$data['user']}\nProduct: {$data['product']}\nAmount: \${$data['amount']}",
            'payment_received' => "**Payment Received**\nUser: {$data['user']}\nInvoice: {$data['invoice']}\nAmount: \${$data['amount']}",
            'server_suspended' => "**Server Suspended**\nUser: {$data['user']}\nServer: {$data['server']}\nReason: Non-payment",
            'ticket_created' => "**New Ticket**\nUser: {$data['user']}\nSubject: {$data['subject']}",
            default => "**Notification**\n".json_encode($data),
        };

        $this->sendMessage($channelId, $message);
    }

    public function getGuildChannels(): ?array
    {
        if (! $this->guildId) {
            return null;
        }

        return $this->apiRequest('get', "/guilds/{$this->guildId}/channels");
    }

    public function createChannel(string $name, string $type = 'GUILD_TEXT'): ?array
    {
        if (! $this->guildId) {
            return null;
        }

        return $this->apiRequest('post', "/guilds/{$this->guildId}/channels", [
            'name' => $name,
            'type' => $type === 'GUILD_TEXT' ? 0 : 2,
        ]);
    }
}
