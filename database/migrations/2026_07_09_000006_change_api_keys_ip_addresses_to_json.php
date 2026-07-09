<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('api_keys', function (Blueprint $table) {
            $table->json('ip_addresses')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('api_keys', function (Blueprint $table) {
            $table->dropColumn('ip_addresses');
        });

        Schema::table('api_keys', function (Blueprint $table) {
            $table->string('ip_addresses')->nullable()->after('type');
        });
    }
};
