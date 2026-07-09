<?php

use App\Jobs\GenerateInvoices;
use App\Jobs\ProcessBillingCycle;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schedule;
use Spatie\Permission\Models\Role;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new GenerateInvoices)->dailyAt('00:01');
Schedule::job(new ProcessBillingCycle)->dailyAt('00:15');

Artisan::command('billing:run', function () {
    dispatch_sync(new GenerateInvoices);
    dispatch_sync(new ProcessBillingCycle);
    $this->info('Billing cycle processed successfully.');
})->purpose('Run billing cycle manually');

Artisan::command('app:init', function () {
    $url = $this->ask('App URL', config('app.url'));
    $name = $this->ask('Site name', config('app.name'));

    Setting::set('site_url', $url);
    Setting::set('site_name', $name);

    $this->info('Application initialized successfully.');
})->purpose('Initialize the application URL and name');

Artisan::command('app:user:create', function () {
    $firstName = $this->ask('First name');
    $lastName = $this->ask('Last name');
    $email = $this->ask('Email');
    $password = $this->secret('Password');

    $user = User::create([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'name' => trim("$firstName $lastName"),
        'email' => $email,
        'password' => Hash::make($password),
        'email_verified' => true,
    ]);

    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'customer']);
    $user->assignRole('admin');

    $this->info("Admin user '{$email}' created successfully.");
})->purpose('Create an admin user');

Artisan::command('app:fetch-emails', function () {
    $this->call(\App\Console\Commands\FetchTicketEmails::class);
})->purpose('Fetch ticket emails via IMAP');
