<?php

use App\Foundation\Database\TenantIsolationPolicy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('default_unit');
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
        });

        TenantIsolationPolicy::tenantIsolationUp('services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolationPolicy::tenantIsolationDown('services');
        Schema::dropIfExists('services');
    }
};
