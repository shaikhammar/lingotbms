<?php

use App\Foundation\Database\TenantIsolationPolicy;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        TenantIsolationPolicy::tenantIsolationUp('notes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolationPolicy::tenantIsolationDown('notes');
    }
};
