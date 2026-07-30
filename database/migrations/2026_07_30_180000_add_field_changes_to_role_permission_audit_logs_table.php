<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('role_permission_audit_logs', function (Blueprint $table) {
            $table->json('field_changes')->nullable()->after('meta');
        });
    }

    public function down(): void
    {
        Schema::table('role_permission_audit_logs', function (Blueprint $table) {
            $table->dropColumn('field_changes');
        });
    }
};
