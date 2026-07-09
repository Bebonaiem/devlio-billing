<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Console\Command;

class FetchTicketEmails extends Command
{
    protected $signature = 'app:fetch-emails';

    protected $description = 'Fetch ticket emails via IMAP and create ticket messages';

    public function handle(): int
    {
        if (! extension_loaded('imap')) {
            $this->error('PHP IMAP extension is not installed.');

            return Command::FAILURE;
        }

        $host = Setting::get('email_host');
        $port = Setting::get('email_port', '993');
        $email = Setting::get('email_address');
        $password = Setting::get('email_password');

        if (! $host || ! $email || ! $password) {
            $this->error('Email piping settings are not configured.');

            return Command::FAILURE;
        }

        $mailbox = @imap_open("{mail.{$host}:{$port}/imap/ssl}INBOX", $email, $password);

        if (! $mailbox) {
            $this->error('Failed to connect to IMAP server: '.imap_last_error());

            return Command::FAILURE;
        }

        $unread = imap_search($mailbox, 'UNSEEN');

        if (! $unread || empty($unread)) {
            $this->info('No unread emails found.');

            imap_close($mailbox);

            return Command::SUCCESS;
        }

        $processed = 0;

        foreach ($unread as $emailNumber) {
            try {
                $header = imap_headerinfo($mailbox, $emailNumber);
                $subject = $header->subject ?? '';
                $from = $header->fromaddress ?? '';
                $body = $this->getBody($mailbox, $emailNumber);

                if (! preg_match('/Re:\s*\[Ticket\s*#(\d+)\]/i', $subject, $matches)) {
                    continue;
                }

                $ticketId = (int) $matches[1];
                $ticket = Ticket::find($ticketId);

                if (! $ticket) {
                    $this->warn("Ticket #{$ticketId} not found. Skipping.");

                    continue;
                }

                $user = $this->resolveUser($header, $ticket);

                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user?->id,
                    'message' => $body,
                ]);

                if ($ticket->status === 'awaiting_admin') {
                    $ticket->update(['status' => 'open']);
                }

                imap_setflag_full($mailbox, (string) $emailNumber, '\\Seen');
                $processed++;

                $this->info("Processed email for Ticket #{$ticketId} from: {$from}");
            } catch (\Exception $e) {
                $this->error("Error processing email #{$emailNumber}: {$e->getMessage()}");
            }
        }

        imap_close($mailbox);

        $this->info("Done. Processed {$processed} email(s).");

        return Command::SUCCESS;
    }

    private function getBody($mailbox, int $emailNumber): string
    {
        $structure = imap_fetchstructure($mailbox, $emailNumber);

        $body = '';

        if (isset($structure->parts)) {
            foreach ($structure->parts as $partNumber => $part) {
                $encoding = $part->encoding ?? 0;
                $text = imap_fetchbody($mailbox, $emailNumber, $partNumber + 1);

                if ($encoding === 1) {
                    $text = imap_base64($text);
                } elseif ($encoding === 2) {
                    $text = imap_qprint($text);
                }

                if (str_contains($part->subtype ?? '', 'PLAIN')) {
                    $body .= $text;
                }
            }
        } else {
            $body = imap_body($mailbox, $emailNumber);
        }

        return trim($body);
    }

    private function resolveUser(object $header, Ticket $ticket): ?User
    {
        $fromEmail = strtolower(trim($header->from[0]->mailbox.'@'.$header->from[0]->host));

        if ($ticket->user && strtolower($ticket->user->email) === $fromEmail) {
            return $ticket->user;
        }

        $user = User::whereRaw('LOWER(email) = ?', [$fromEmail])->first();

        if ($user) {
            return $user;
        }

        if ($ticket->user) {
            return $ticket->user;
        }

        return null;
    }
}
