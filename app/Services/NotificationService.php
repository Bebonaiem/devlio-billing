<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Mail\PasswordResetMail;
use App\Mail\ServiceActivatedMail;
use App\Mail\ServiceSuspendedMail;
use App\Mail\ServiceTerminatedMail;
use App\Mail\TicketReplyMail;
use App\Mail\WelcomeMail;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\NotificationTemplate;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    private const TEMPLATE_MAP = [
        'invoice.created' => 'invoice',
        'service.activated' => 'service-activated',
        'service.suspended' => 'service-suspended',
        'service.terminated' => 'service-terminated',
        'ticket.reply' => 'ticket-reply',
        'user.registered' => 'welcome',
        'password.reset' => 'password-reset',
    ];

    public function send(User $user, string $templateKey, array $data = []): bool
    {
        try {
            $template = NotificationTemplate::where('key', $templateKey)->first();

            if (! $template || ! $template->enabled) {
                return false;
            }

            $preference = $this->getPreference($user, $template);

            $mailEnabled = $template->mail_enabled;
            $inAppEnabled = $template->in_app_enabled;

            if ($preference) {
                $mailEnabled = $preference->mail_enabled;
                $inAppEnabled = $preference->in_app_enabled;
            }

            $success = true;

            if ($mailEnabled) {
                $mailResult = $this->sendMail($user, $template, $data);
                if (! $mailResult) {
                    $success = false;
                }
            }

            if ($inAppEnabled) {
                $this->createNotification($user, $template, $data);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error("Notification send failed for user {$user->id} with template {$templateKey}: ".$e->getMessage());

            return false;
        }
    }

    private function sendMail(User $user, NotificationTemplate $template, array $data): bool
    {
        $mailable = $this->buildMailable($template->key, $data, $user);

        if (! $mailable) {
            return false;
        }

        Mail::to($user->email)->send($mailable);

        return true;
    }

    private function buildMailable(string $key, array $data, User $user): ?object
    {
        return match ($key) {
            'invoice.created' => new InvoiceMail(
                $data['invoice'] ?? new Invoice,
                $user
            ),
            'service.activated' => new ServiceActivatedMail(
                $data['service'] ?? new Service,
                $user
            ),
            'service.suspended' => new ServiceSuspendedMail(
                $data['service'] ?? new Service,
                $user
            ),
            'service.terminated' => new ServiceTerminatedMail(
                $data['service'] ?? new Service,
                $user
            ),
            'ticket.reply' => new TicketReplyMail(
                $data['ticket'] ?? new Ticket,
                $user,
                $data['message'] ?? ''
            ),
            'user.registered' => new WelcomeMail($user),
            'password.reset' => new PasswordResetMail(
                $user,
                $data['reset_url'] ?? ''
            ),
            default => null,
        };
    }

    private function createNotification(User $user, NotificationTemplate $template, array $data): void
    {
        $title = $this->renderTemplate($template->in_app_title ?? $template->subject, $data);
        $body = $this->renderTemplate($template->in_app_body ?? $template->body, $data);

        Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'url' => $data['url'] ?? null,
        ]);
    }

    private function getPreference(User $user, NotificationTemplate $template): ?NotificationPreference
    {
        return NotificationPreference::where('user_id', $user->id)
            ->where('notification_template_id', $template->id)
            ->first();
    }

    private function renderTemplate(string $template, array $data): string
    {
        $replacements = [];

        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if (method_exists($value, 'toArray')) {
                    $arrayData = $value->toArray();
                    foreach ($arrayData as $arrKey => $arrValue) {
                        $replacements["{{{$key}.{$arrKey}}}"] = (string) $arrValue;
                    }
                }
                $replacements["{{{$key}}}"] = (string) $value;
            } else {
                $replacements["{{{$key}}}"] = (string) $value;
            }
        }

        $replacements['{{app_name}}'] = config('app.name', 'Application');

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
