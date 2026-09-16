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
        // Create the tenant_settings table with necessary columns
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('base_currency', 3)->default('USD');
            $table->string('invoice_prefix')->default('INV');
            $table->unsignedInteger('invoice_next_number')->default(1);
            $table->unsignedInteger('default_payment_terms_days')->default(30);
            $table->string('timezone')->default('UTC');
            $table->timestamps();
        });

        // Apply tenant isolation policy to the tenant_settings table
        TenantIsolationPolicy::tenantIsolationUp('tenant_settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse tenant isolation policy before dropping the table
        TenantIsolationPolicy::tenantIsolationDown('tenant_settings');
        // Drop the tenant_settings table after reversing tenant isolation policy
        Schema::dropIfExists('tenant_settings');
    }
};
