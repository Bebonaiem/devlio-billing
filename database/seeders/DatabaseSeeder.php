<?php

namespace Database\Seeders;

use App\Models\Extension;
use App\Models\NotificationPreference;
use App\Models\NotificationTemplate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'customer']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gamebilling.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // Default settings
        Setting::firstOrCreate(['key' => 'site_name'], ['value' => 'GameBilling']);
        Setting::firstOrCreate(['key' => 'currency'], ['value' => 'USD']);
        Setting::firstOrCreate(['key' => 'tax_rate'], ['value' => '0']);
        Setting::firstOrCreate(['key' => 'invoice_prefix'], ['value' => 'INV-']);
        Setting::firstOrCreate(['key' => 'grace_days'], ['value' => '3']);
        Setting::firstOrCreate(['key' => 'terminate_days'], ['value' => '14']);
        Setting::firstOrCreate(['key' => 'affiliate_rate'], ['value' => '10']);

        // Payment gateways
        Extension::firstOrCreate(['extension' => 'stripe'], [
            'name' => 'Stripe',
            'type' => 'gateway',
            'enabled' => true,
        ]);
        Extension::firstOrCreate(['extension' => 'paypal'], [
            'name' => 'PayPal',
            'type' => 'gateway',
            'enabled' => true,
        ]);
        Extension::firstOrCreate(['extension' => 'credit'], [
            'name' => 'Credit',
            'type' => 'gateway',
            'enabled' => true,
        ]);

        $this->seedNotificationTemplates();
        $this->seedDefaultNotificationPreferences();
    }

    private function seedNotificationTemplates(): void
    {
        $templates = [
            [
                'key' => 'invoice.created',
                'name' => 'Invoice Created',
                'subject' => 'Invoice #{{number}}',
                'body' => 'Your invoice for {{amount}} is now pending.',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => true,
                'in_app_title' => 'New Invoice',
                'in_app_body' => 'Invoice #{{number}} for {{amount}} is now pending.',
            ],
            [
                'key' => 'service.activated',
                'name' => 'Service Activated',
                'subject' => 'Service Activated',
                'body' => 'Your {{product}} service is now active.',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => true,
                'in_app_title' => 'Service Activated',
                'in_app_body' => 'Your {{product}} service is now active.',
            ],
            [
                'key' => 'service.suspended',
                'name' => 'Service Suspended',
                'subject' => 'Service Suspended',
                'body' => 'Your {{product}} service has been suspended.',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => true,
                'in_app_title' => 'Service Suspended',
                'in_app_body' => 'Your {{product}} service has been suspended.',
            ],
            [
                'key' => 'service.terminated',
                'name' => 'Service Terminated',
                'subject' => 'Service Terminated',
                'body' => 'Your {{product}} service has been terminated.',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => true,
                'in_app_title' => 'Service Terminated',
                'in_app_body' => 'Your {{product}} service has been terminated.',
            ],
            [
                'key' => 'ticket.reply',
                'name' => 'Ticket Reply',
                'subject' => 'Ticket Reply - #{{ticket_id}}',
                'body' => '{{message}}',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => true,
                'in_app_title' => 'Ticket Reply',
                'in_app_body' => 'New reply on ticket #{{ticket_id}}.',
            ],
            [
                'key' => 'user.registered',
                'name' => 'User Registered',
                'subject' => 'Welcome to {{app_name}}',
                'body' => 'Welcome! Your account is ready.',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => true,
                'in_app_title' => 'Welcome!',
                'in_app_body' => 'Your account has been created successfully.',
            ],
            [
                'key' => 'password.reset',
                'name' => 'Password Reset',
                'subject' => 'Password Reset',
                'body' => 'Click here to reset: {{reset_url}}',
                'enabled' => true,
                'mail_enabled' => true,
                'in_app_enabled' => false,
                'in_app_title' => 'Password Reset',
                'in_app_body' => 'A password reset was requested for your account.',
            ],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::firstOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }

    private function seedDefaultNotificationPreferences(): void
    {
        $templates = NotificationTemplate::all();
        $users = User::all();

        foreach ($users as $user) {
            foreach ($templates as $template) {
                NotificationPreference::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'notification_template_id' => $template->id,
                    ],
                    [
                        'mail_enabled' => $template->mail_enabled,
                        'in_app_enabled' => $template->in_app_enabled,
                    ]
                );
            }
        }
    }
}
