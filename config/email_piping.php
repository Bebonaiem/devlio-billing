<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IMAP Configuration for Email Piping
    |--------------------------------------------------------------------------
    */

    'host' => env('IMAP_HOST'),
    'port' => env('IMAP_PORT', 993),
    'username' => env('IMAP_USERNAME'),
    'password' => env('IMAP_PASSWORD'),
    'encryption' => env('IMAP_ENCRYPTION', 'ssl'),

    /*
    |--------------------------------------------------------------------------
    | Ticket Matching
    |--------------------------------------------------------------------------
    */

    'subject_pattern' => '/\[Ticket #(\d+)\]/',
    'reply_pattern' => '/RE:\s*.*#(\d+)/i',

];
