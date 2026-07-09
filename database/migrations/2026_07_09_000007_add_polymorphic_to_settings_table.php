<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'type')) {
                $table->string('type')->default('string')->after('value');
            }
            if (! Schema::hasColumn('settings', 'encrypted')) {
                $table->boolean('encrypted')->default(false)->after('type');
            }
            if (! Schema::hasColumn('settings', 'settingable_id')) {
                $table->unsignedBigInteger('settingable_id')->nullable()->after('encrypted');
            }
            if (! Schema::hasColumn('settings', 'settingable_type')) {
                $table->string('settingable_type')->nullable()->after('settingable_id');
            }

            try {
                $table->dropUnique(['key']);
            } catch (Throwable) {
            }

            try {
                $table->unique(['key', 'settingable_id', 'settingable_type']);
            } catch (Throwable) {
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            try {
                $table->dropUnique(['key', 'settingable_id', 'settingable_type']);
            } catch (Throwable) {
            }

            try {
                $table->unique(['key']);
            } catch (Throwable) {
            }

            if (Schema::hasColumn('settings', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('settings', 'encrypted')) {
                $table->dropColumn('encrypted');
            }
            if (Schema::hasColumn('settings', 'settingable_id')) {
                $table->dropColumn('settingable_id');
            }
            if (Schema::hasColumn('settings', 'settingable_type')) {
                $table->dropColumn('settingable_type');
            }
        });
    }
};
