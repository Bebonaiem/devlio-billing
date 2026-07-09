<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop all existing custom tables (reverse dependency order)
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('servers');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('products');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('cache');

        // Recreate cache table
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
            $table->index('expiration');
        });

        // Recreate permission tables (Spatie)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        // Modify users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('tfa_secret')->after('password')->nullable();
            $table->unsignedBigInteger('role_id')->after('tfa_secret')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
            $table->boolean('email_verified')->default(false)->after('email_verified_at');
            $table->string('affiliate_code', 20)->nullable()->unique()->after('email_verified');
            $table->unsignedBigInteger('referred_by')->nullable()->after('affiliate_code');
            $table->foreign('referred_by')->references('id')->on('users')->nullOnDelete();
        });

        // =====================================================
        // CATEGORIES
        // =====================================================
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
        });

        // =====================================================
        // PRODUCTS
        // =====================================================
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('per_user_limit')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('hidden')->default(false);
            $table->boolean('allow_quantity')->default(false);
            $table->unsignedBigInteger('server_id')->nullable();
            $table->timestamps();
        });

        // =====================================================
        // CONFIG OPTIONS (price-bearing configurable options)
        // =====================================================
        Schema::create('config_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('env_variable')->nullable();
            $table->enum('type', ['dropdown', 'radio', 'checkbox', 'text', 'number'])->default('dropdown');
            $table->integer('sort')->default(0);
            $table->boolean('hidden')->default(false);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('upgradable')->default(false);
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('config_options')->nullOnDelete();
        });

        Schema::create('config_option_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('config_option_id')->constrained()->cascadeOnDelete();
            $table->unique(['product_id', 'config_option_id']);
        });

        // =====================================================
        // CURRENCIES
        // =====================================================
        Schema::create('currencies', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->string('prefix')->default('');
            $table->string('suffix')->default('');
            $table->string('format')->default('1,000.00');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        // =====================================================
        // PLANS (polymorphic: can belong to Product OR ConfigOption)
        // =====================================================
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['free', 'one-time', 'recurring'])->default('recurring');
            $table->integer('billing_period')->nullable();
            $table->enum('billing_unit', ['day', 'week', 'month', 'year'])->nullable();
            $table->integer('sort')->default(0);
            $table->morphs('priceable'); // priceable_id, priceable_type
            $table->integer('memory')->nullable();
            $table->integer('cpu')->nullable();
            $table->integer('disk')->nullable();
            $table->integer('swap')->default(0);
            $table->integer('databases')->default(0);
            $table->integer('backups')->default(0);
            $table->integer('allocations')->default(1);
            $table->unsignedBigInteger('nest_id')->nullable();
            $table->unsignedBigInteger('egg_id')->nullable();
            $table->timestamps();
        });

        // =====================================================
        // PRICES (per currency per plan)
        // =====================================================
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('currency_code', 10)->default('USD');
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('setup_fee', 12, 2)->default(0);
            $table->timestamps();
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();
        });

        // =====================================================
        // ORDERS (simplified: just links user to currency)
        // =====================================================
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency_code', 10)->default('USD');
            $table->timestamps();
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();
        });

        // =====================================================
        // SERVICES (core entity: active subscriptions)
        // =====================================================
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->string('subscription_id')->nullable();
            $table->enum('status', ['pending', 'active', 'cancelled', 'suspended'])->default('pending');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('currency_code', 10)->default('USD');
            $table->unsignedBigInteger('billing_agreement_id')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();
        });

        // =====================================================
        // SERVICE CONFIGS (config option values per service)
        // =====================================================
        Schema::create('service_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('config_option_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('config_value_id')->nullable();
            $table->timestamps();
            $table->foreign('config_value_id')->references('id')->on('config_options')->nullOnDelete();
        });

        // =====================================================
        // INVOICES
        // =====================================================
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency_code', 10)->default('USD');
            $table->timestamp('due_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled', 'overdue', 'refunded'])->default('pending');
            $table->timestamps();
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();
        });

        // =====================================================
        // INVOICE ITEMS
        // =====================================================
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->string('description');
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->timestamps();
            $table->index(['reference_id', 'reference_type']);
        });

        // =====================================================
        // EXTENSIONS (plugin system) — must come before invoice_transactions
        // =====================================================
        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('enabled')->default(false);
            $table->string('extension');
            $table->enum('type', ['gateway', 'server', 'other'])->default('other');
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================
        // INVOICE TRANSACTIONS (renamed from transactions)
        // =====================================================
        Schema::create('invoice_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('fee', 12, 2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['processing', 'succeeded', 'failed'])->default('processing');
            $table->boolean('is_credit_transaction')->default(false);
            $table->timestamps();
            $table->foreign('gateway_id')->references('id')->on('extensions')->nullOnDelete();
        });

        // =====================================================
        // INVOICE SNAPSHOTS (frozen data on payment)
        // =====================================================
        Schema::create('invoice_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->json('properties')->nullable();
            $table->string('tax_name')->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->string('tax_country')->nullable();
            $table->text('bill_to')->nullable();
            $table->timestamps();
        });

        // =====================================================
        // CREDITS (per user per currency)
        // =====================================================
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency_code', 10)->default('USD');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'currency_code']);
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();
        });

        // =====================================================
        // COUPONS
        // =====================================================
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->enum('time', ['once', 'recurring'])->default('once');
            $table->string('code')->unique();
            $table->decimal('value', 12, 2)->default(0);
            $table->integer('max_uses')->nullable();
            $table->integer('max_uses_per_user')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('recurring')->default(false);
            $table->enum('applies_to', ['all', 'specific'])->default('all');
            $table->timestamps();
        });

        Schema::create('coupon_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['coupon_id', 'product_id']);
        });

        // =====================================================
        // CART (polymorphic session-based)
        // =====================================================
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('ulid', 26)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('currency_code', 10)->default('USD');
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->foreign('currency_code')->references('code')->on('currencies')->cascadeOnDelete();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->json('config_options')->nullable();
            $table->json('checkout_config')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // =====================================================
        // TICKETS (with department and assignment)
        // =====================================================
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('department')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->timestamps();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->unsignedBigInteger('ticket_mail_log_id')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_message_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid');
            $table->string('file_path');
            $table->string('original_name');
            $table->timestamps();
        });

        // =====================================================
        // NOTIFICATIONS (in-app)
        // =====================================================
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->boolean('enabled')->default(true);
            $table->text('body')->nullable();
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->boolean('mail_enabled')->default(true);
            $table->boolean('in_app_enabled')->default(true);
            $table->string('in_app_title')->nullable();
            $table->text('in_app_body')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('mail_enabled')->default(true);
            $table->boolean('in_app_enabled')->default(true);
            $table->foreignId('notification_template_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'notification_template_id']);
        });

        // =====================================================
        // BILLING AGREEMENTS (stored payment methods)
        // =====================================================
        Schema::create('billing_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('ulid', 26)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gateway_id')->nullable()->constrained('extensions')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('external_reference')->nullable();
            $table->string('type')->nullable();
            $table->timestamp('expiry')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // =====================================================
        // TAX RATES
        // =====================================================
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2)->default(0);
            $table->string('country')->default('all');
            $table->timestamps();
        });

        // =====================================================
        // PROPERTIES (custom field values)
        // =====================================================
        Schema::create('custom_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('allowed_values')->nullable();
            $table->boolean('show_on_invoice')->default(false);
            $table->string('type')->default('text');
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_property_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->index(['model_id', 'model_type']);
        });

        // =====================================================
        // API KEYS
        // =====================================================
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('permissions')->nullable();
            $table->string('token')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->default('admin');
            $table->string('ip_addresses')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        // =====================================================
        // PAYMENT METHODS (keep for Stripe/PayPal)
        // =====================================================
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gateway');
            $table->string('gateway_customer_id')->nullable();
            $table->string('gateway_payment_method_id')->nullable();
            $table->string('type')->nullable();
            $table->string('last_four', 4)->nullable();
            $table->string('brand')->nullable();
            $table->integer('exp_month')->nullable();
            $table->integer('exp_year')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // =====================================================
        // SETTINGS
        // =====================================================
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // =====================================================
        // SERVICE CANCELLATIONS
        // =====================================================
        Schema::create('service_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->enum('type', ['user_requested', 'overdue', 'admin'])->default('user_requested');
            $table->timestamps();
        });

        // =====================================================
        // SERVICE UPGRADES
        // =====================================================
        Schema::create('service_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });

        // =====================================================
        // PRODUCT UPGRADES
        // =====================================================
        Schema::create('product_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('upgrade_id')->constrained('products')->cascadeOnDelete();
            $table->unique(['product_id', 'upgrade_id']);
        });

        // =====================================================
        // AFFILIATE COMMISSIONS
        // =====================================================
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('rate', 5, 2)->default(10.00);
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // Seed default currency
        DB::table('currencies')->insert([
            ['code' => 'USD', 'name' => 'US Dollar', 'prefix' => '$', 'suffix' => '', 'format' => '1,000.00', 'enabled' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        // Drop all new tables
        $tables = [
            'product_upgrades', 'service_upgrades', 'service_cancellations',
            'settings', 'payment_methods', 'api_keys', 'properties', 'custom_properties',
            'tax_rates', 'billing_agreements', 'extensions', 'notification_preferences',
            'notification_templates', 'notifications', 'ticket_message_attachments',
            'ticket_messages', 'tickets', 'coupon_products', 'coupons', 'credits',
            'invoice_snapshots', 'invoice_transactions', 'invoice_items', 'invoices',
            'service_configs', 'services', 'orders', 'prices', 'plans',
            'config_option_products', 'config_options', 'products', 'categories',
            'affiliate_commissions',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        // Restore original schema
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['first_name', 'last_name', 'tfa_secret', 'role_id', 'email_verified']);
        });

        Schema::dropIfExists('currencies');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
