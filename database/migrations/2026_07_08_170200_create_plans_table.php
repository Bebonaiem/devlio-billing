<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('cpu')->default(100);
            $table->integer('memory')->default(1024);
            $table->integer('disk')->default(10240);
            $table->integer('swap')->default(0);
            $table->integer('databases')->default(0);
            $table->integer('backups')->default(0);
            $table->integer('allocations')->default(1);
            $table->unsignedBigInteger('nest_id')->nullable();
            $table->unsignedBigInteger('egg_id')->nullable();
            $table->string('billing_cycle'); // monthly, quarterly, semi_annually, annually
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('setup_fee', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
