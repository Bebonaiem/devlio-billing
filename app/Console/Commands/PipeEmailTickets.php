¿<?php
namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PipeEmailTickets extends Command
{
    protected $signature = 'tickets:pipe-emails {--host= : IMAP host} {--port=993 : IMAP port} {--username= : IMAP username} {--password= : IMAP password}';

    protected $description = 'Pipe incoming emails to support tickets';

    public function handle(): int
    {
        $host = $this->option('host') ?? config('email_piping.host');
        $port = $this->option('port') ?? config('email_piping.port', 993);
        $username = $this->option('username') ?? config('email_piping.username');
        $password = $this->option('password') ?? config('email_piping.password');

        if (! $host || ! $username || ! $password) {
            $this->error('IMAP credentials not configured. Set them in config/email_piping.php or pass as options.');

            return self::FAILURE;
        }

        try {
            $inbox = $this->connectImap($host, $port, $username, $password);

            if (! $inbox) {
                $this->error('Failed to connect to IMAP server.');

                return self::FAILURE;
            }

            $emails = imap_search($inbox, 'UNSEEN');

            if (! $emails) {
                $this->info('No new emails found.');

                return self::SUCCESS;
            }

            $processed = 0;

            foreach ($emails as $emailNumber) {
                $this->processEmail($inbox, $emailNumber);
                $processed++;
            }

            imap_close($inbox);
            imap_errors();

            $this->info("Processed {$processed} email(s).");

            return self::SUCCESS;

        } catch (\Exception $e) {
            Log::error('Email piping failed: '.$e->getMessage());
            $this->error('Error: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    private function connectImap(string $host, int $port, string $username, string $password)
    {
        if (! function_exists('imap_open')) {
            $this->error('PHP IMAP extension is not installed.');

            return false;
        }

        $inbox = imap_open('{'.$host.':'.$port.'/imap/ssl}INBOX', $username, $password);

        return $inbox ?: false;
    }

    private function processEmail($inbox, int $emailNumber): void
    {
        $header = imap_headerinfo($inbox, $emailNumber);
        $subject = $this->decodeHeader($header->subject);
        $from = $header->from[0]->mailbox.'@'.$header->from[0]->host;
        $fromName = $header->from[0]->personal ?? $from;

        $body = $this->getCardBody($inbox, $emailNumber);

        $ticketNumber = $this->extractTicketNumber($subject);

        if ($ticketNumber) {
            $this->appendToExistingTicket($ticketNumber, $from, $fromName, $body);
        } else {
            $this->createNewTicket($from, $fromName, $subject, $body);
        }

        imap_setflag_full($inbox, (string) $emailNumber, '\\Seen');
    }

    private function extractTicketNumber(string $subject): ?string
    {
        if (preg_match('/\[Ticket #(\d+)\]/', $subject, $matches)) {
            return $matches[1];
        }

        if (preg_match('/RE:.*#(\d+)/', $subject, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function appendToExistingTicket(string $ticketNumber, string $email, string $name, string $body): void
    {
        $ticket = Ticket::where('id', $ticketNumber)->first();

        if (! $ticket) {
            $this->warn("Ticket #{$ticketNumber} not found. Creating new ticket.");

            $this->createNewTicket($email, $name, "Re: Ticket #{$ticketNumber}", $body);

            return;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->warn("User not found for email: {$email}");

            return;
        }

        if ($ticket->user_id !== $user->id && ! $user->isAdmin()) {
            $this->warn("User {$email} is not authorized to reply to ticket #{$ticketNumber}");

            return;
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $body,
        ]);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        $this->info("Appended reply to ticket #{$ticketNumber}");
    }

    private function createNewTicket(string $email, string $name, string $subject, string $body): void
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->warn("No user found for email: {$email}. Skipping.");

            return;
        }

        $cleanSubject = preg_replace('/^(RE:|FW:|FWD:)\s*/i', '', $subject);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => $cleanSubject,
            'status' => 'open',
            'priority' => 'medium',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $body,
        ]);

        $this->info("Created ticket #{$ticket->id} from {$email}");
    }

    private function decodeHeader(string $header): string
    {
        $decoded = imap_utf8($header);

        if (preg_match('/=\?(.+?)\?([BQ])\?(.+?)\?=/i', $decoded, $matches)) {
            $decoded = mb_decode_mimeheader($decoded);
        }

        return $decoded;
    }

    private function getCardBody($inbox, int $emailNumber): string
    {
        $body = '';

        $structure = imap_fetchstructure($inbox, $emailNumber);

        if (! $structure->parts) {
            $body = imap_body($inbox, $emailNumber);

            return $this->cleanBody($body);
        }

        foreach ($structure->parts as $partNumber => $part) {
            if ($part->type == 0 && $part->ifsubtype == 0) {
                $body = imap_fetchbody($inbox, $emailNumber, $partNumber + 1);

                if ($part->encoding == 1) {
                    $body = imap_base64($body);
                } elseif ($part->encoding == 2) {
                    $body = imap_qprint($body);
                }

                return $this->cleanBody($body);
            }
        }

        return $body;
    }

    private function cleanBody(string $body): string
    {
        $body = strip_tags($body);
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = preg_replace('/\s+/', ' ', $body);
        $body = trim($body);

        return $body;
    }
}
