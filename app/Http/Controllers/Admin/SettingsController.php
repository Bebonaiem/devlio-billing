¿<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $keys = [
            'site_name', 'site_description', 'site_url', 'default_currency', 'currency_format',
            'registration_enabled', 'tax_rate', 'invoice_prefix', 'invoice_due_days',
            'grace_days', 'terminate_days', 'affiliate_rate',
            'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'mail_from_address',
            'pterodactyl_url', 'pterodactyl_api_key',
            'paypal_client_id', 'paypal_secret', 'paypal_webhook_id', 'paypal_sandbox',
            'stripe_key', 'stripe_secret', 'stripe_webhook_secret',
            'email_host', 'email_port', 'email_address', 'email_password',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }
}
