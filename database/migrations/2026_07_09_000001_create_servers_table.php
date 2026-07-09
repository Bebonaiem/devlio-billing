<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pterodactyl_server_id')->nullable();
            $table->string('pterodactyl_server_identifier')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->default('active');
            $table->integer('cpu')->nullable();
            $table->integer('memory')->nullable();
            $table->integer('disk')->nullable();
            $table->string('node')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
