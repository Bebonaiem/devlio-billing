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
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('pterodactyl_server_id')->nullable();
            $table->string('pterodactyl_server_identifier')->nullable();
            $table->string('name');
            $table->string('status')->default('installing'); // installing, active, suspended, terminated
            $table->integer('cpu');
            $table->integer('memory');
            $table->integer('disk');
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
