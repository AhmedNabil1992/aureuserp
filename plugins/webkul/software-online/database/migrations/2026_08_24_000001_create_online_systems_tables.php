<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('logo')->nullable();
            $table->string('base_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);

            // API Configuration
            $table->string('api_driver')->default('rest');
            $table->string('api_base_url')->nullable();
            $table->text('api_token')->nullable();
            $table->text('api_secret')->nullable();
            $table->json('api_headers')->nullable();

            // API Endpoints
            $table->string('create_tenant_endpoint')->default('/api/v1/tenants');
            $table->string('renew_tenant_endpoint')->default('/api/v1/tenants/{tenant_id}/renew');
            $table->string('suspend_tenant_endpoint')->default('/api/v1/tenants/{tenant_id}/suspend');
            $table->string('activate_tenant_endpoint')->default('/api/v1/tenants/{tenant_id}/activate');
            $table->string('delete_tenant_endpoint')->default('/api/v1/tenants/{tenant_id}');
            $table->string('sync_status_endpoint')->default('/api/v1/tenants/{tenant_id}/status');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('online_system_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('online_systems')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->decimal('monthly_price', 12, 2)->default(0.00);
            $table->decimal('annual_price', 12, 2)->default(0.00);
            $table->string('currency_code')->default('EGP');
            $table->integer('trial_days')->default(0);
            $table->integer('max_users')->nullable();
            $table->integer('max_branches')->nullable();
            $table->json('custom_api_payload')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['system_id', 'slug']);
        });

        Schema::create('online_instances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instance_number')->index();
            $table->foreignId('partner_id')->constrained('partners_partners')->cascadeOnDelete();
            $table->foreignId('system_id')->constrained('online_systems')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('online_system_plans')->cascadeOnDelete();

            $table->string('name');
            $table->string('subdomain')->nullable()->index();
            $table->string('custom_domain')->nullable()->index();
            $table->string('instance_url')->nullable();
            $table->string('admin_email')->nullable();
            $table->string('admin_username')->nullable();

            $table->string('billing_cycle')->default('monthly');
            $table->decimal('price', 12, 2)->default(0.00);
            $table->string('status')->default('pending')->index();

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable()->index();
            $table->dateTime('last_renewed_at')->nullable();
            $table->boolean('auto_renew')->default(true);

            // Remote Sync & API State
            $table->string('remote_tenant_id')->nullable()->index();
            $table->json('remote_data')->nullable();
            $table->dateTime('last_api_sync_at')->nullable();
            $table->text('last_api_error')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('online_instance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_id')->constrained('online_instances')->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->cascadeOnDelete();
            $table->string('type')->default('new_subscription');
            $table->string('billing_cycle')->default('monthly');
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('status')->default('paid');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->unsignedBigInteger('move_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_instance_transactions');
        Schema::dropIfExists('online_instances');
        Schema::dropIfExists('online_system_plans');
        Schema::dropIfExists('online_systems');
    }
};
