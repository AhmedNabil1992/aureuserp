<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_instances', function (Blueprint $table): void {
            $table->string('provisioning_request_id')->nullable()->after('remote_tenant_id')->index();
            $table->dateTime('provisioned_at')->nullable()->after('last_api_sync_at');
            $table->dateTime('last_webhook_at')->nullable()->after('provisioned_at');
            $table->dateTime('last_renewal_attempt_at')->nullable()->after('last_renewed_at');
            $table->text('last_renewal_error')->nullable()->after('last_renewal_attempt_at');
            $table->unique(['system_id', 'subdomain'], 'online_instances_system_subdomain_unique');
        });

        Schema::table('online_instance_transactions', function (Blueprint $table): void {
            $table->string('idempotency_key')->nullable()->after('status')->unique();
            $table->string('remote_sync_status')->nullable()->after('idempotency_key')->index();
            $table->unsignedInteger('remote_sync_attempts')->default(0)->after('remote_sync_status');
            $table->text('remote_sync_error')->nullable()->after('remote_sync_attempts');
            $table->dateTime('remote_synced_at')->nullable()->after('remote_sync_error');
        });

        Schema::create('online_tenant_webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('system_id')->constrained('online_systems')->cascadeOnDelete();
            $table->foreignId('instance_id')->nullable()->constrained('online_instances')->nullOnDelete();
            $table->string('event_id')->unique();
            $table->string('event_type')->index();
            $table->string('status')->default('received')->index();
            $table->dateTime('occurred_at');
            $table->json('payload');
            $table->text('error')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
        });

        DB::table('online_systems')
            ->where('create_tenant_endpoint', '/api/v1/tenants')
            ->update(['create_tenant_endpoint' => '/api/tenants']);
        DB::table('online_systems')
            ->where('renew_tenant_endpoint', '/api/v1/tenants/{tenant_id}/renew')
            ->update(['renew_tenant_endpoint' => '/api/tenants/{tenant_id}/renew']);
        DB::table('online_systems')
            ->where('delete_tenant_endpoint', '/api/v1/tenants/{tenant_id}')
            ->update(['delete_tenant_endpoint' => '/api/tenants/{tenant_id}']);
        DB::table('online_systems')
            ->where('sync_status_endpoint', '/api/v1/tenants/{tenant_id}/status')
            ->update(['sync_status_endpoint' => '/api/tenants/{tenant_id}/status']);
    }

    public function down(): void
    {
        Schema::dropIfExists('online_tenant_webhook_events');

        Schema::table('online_instance_transactions', function (Blueprint $table): void {
            $table->dropUnique(['idempotency_key']);
            $table->dropIndex(['remote_sync_status']);
            $table->dropColumn([
                'idempotency_key',
                'remote_sync_status',
                'remote_sync_attempts',
                'remote_sync_error',
                'remote_synced_at',
            ]);
        });

        Schema::table('online_instances', function (Blueprint $table): void {
            $table->dropUnique('online_instances_system_subdomain_unique');
            $table->dropIndex(['provisioning_request_id']);
            $table->dropColumn([
                'provisioning_request_id',
                'provisioned_at',
                'last_webhook_at',
                'last_renewal_attempt_at',
                'last_renewal_error',
            ]);
        });
    }
};
