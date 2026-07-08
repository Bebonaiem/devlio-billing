<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'customer']);

        // Default settings
        Setting::firstOrCreate(['key' => 'site_name'], ['value' => 'GameBilling']);
        Setting::firstOrCreate(['key' => 'currency'], ['value' => 'USD']);
        Setting::firstOrCreate(['key' => 'tax_rate'], ['value' => '0']);
        Setting::firstOrCreate(['key' => 'invoice_prefix'], ['value' => 'INV-']);
        Setting::firstOrCreate(['key' => 'grace_days'], ['value' => '3']);
        Setting::firstOrCreate(['key' => 'terminate_days'], ['value' => '14']);
        Setting::firstOrCreate(['key' => 'affiliate_rate'], ['value' => '10']);
    }
}
