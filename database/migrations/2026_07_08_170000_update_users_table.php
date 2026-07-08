<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pterodactyl_user_id')->nullable()->after('password');
            $table->string('pterodactyl_api_key')->nullable()->after('pterodactyl_user_id');
            $table->decimal('credit_balance', 12, 2)->default(0)->after('pterodactyl_api_key');
            $table->string('affiliate_code', 20)->unique()->nullable()->after('credit_balance');
            $table->unsignedBigInteger('referred_by')->nullable()->after('affiliate_code');
            $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['pterodactyl_user_id', 'pterodactyl_api_key', 'credit_balance', 'affiliate_code', 'referred_by']);
        });
    }
};
