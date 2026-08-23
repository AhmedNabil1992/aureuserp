<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_support_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('technical_support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_number')->index();
            $table->foreignId('partner_id')->constrained('partners_partners')->cascadeOnDelete();
            $table->string('service_type')->default('software')->index();

            // Service References
            $table->unsignedBigInteger('program_id')->nullable()->index();
            $table->unsignedBigInteger('license_id')->nullable()->index();
            $table->unsignedBigInteger('cloud_id')->nullable()->index();
            $table->nullableMorphs('service_item', 'tech_supp_item_idx');

            // Metadata & Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->longText('content');
            $table->string('file_path')->nullable();
            $table->string('status')->default('open')->index();
            $table->string('priority')->default('normal')->index();

            $table->boolean('is_unread_admin')->default(true)->index();
            $table->boolean('is_unread_client')->default(false)->index();
            $table->boolean('reopened')->default(false);
            $table->timestamp('first_closed_at')->nullable();
            $table->timestamp('last_closed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('technical_support_ticket_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('technical_support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->nullOnDelete();
            $table->string('type')->default('message');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });

        Schema::create('technical_support_ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable', 'tech_supp_att_idx');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('technical_support_ticket_tag', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained('technical_support_tickets')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('technical_support_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['ticket_id', 'tag_id']);
        });

        Schema::create('technical_support_ticket_assignees', function (Blueprint $table) {
            $table->foreignId('ticket_id')->constrained('technical_support_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['ticket_id', 'user_id']);
        });

        Schema::create('technical_support_service_staff', function (Blueprint $table) {
            $table->id();
            $table->string('service_type')->index();
            $table->unsignedBigInteger('service_reference_id')->nullable()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['service_type', 'service_reference_id', 'user_id'], 'tech_support_staff_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_support_service_staff');
        Schema::dropIfExists('technical_support_ticket_assignees');
        Schema::dropIfExists('technical_support_ticket_tag');
        Schema::dropIfExists('technical_support_ticket_attachments');
        Schema::dropIfExists('technical_support_ticket_events');
        Schema::dropIfExists('technical_support_tickets');
        Schema::dropIfExists('technical_support_tags');
    }
};
