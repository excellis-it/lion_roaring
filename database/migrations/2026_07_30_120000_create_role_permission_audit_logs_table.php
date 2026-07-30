<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('role_permission_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('source')->default('pma');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name')->nullable();
            $table->string('actor_email')->nullable();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_user_name')->nullable();
            $table->string('target_user_email')->nullable();
            $table->unsignedBigInteger('target_country_id')->nullable()->index();
            $table->unsignedBigInteger('role_template_id')->nullable()->index();
            $table->string('role_template_name')->nullable();
            $table->string('old_role_name')->nullable();
            $table->string('new_role_name')->nullable();
            $table->string('old_user_type')->nullable();
            $table->string('new_user_type')->nullable();
            $table->json('old_permissions')->nullable();
            $table->json('new_permissions')->nullable();
            $table->json('permissions_added')->nullable();
            $table->json('permissions_removed')->nullable();
            $table->unsignedBigInteger('old_membership_tier_id')->nullable();
            $table->string('old_membership_tier_name')->nullable();
            $table->unsignedBigInteger('new_membership_tier_id')->nullable();
            $table->string('new_membership_tier_name')->nullable();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country_code')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('action');
            $table->index('source');
            $table->index('actor_id');
            $table->index('target_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission_audit_logs');
    }
};
