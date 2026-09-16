<?php

namespace App\Foundation\Database;

use Illuminate\Support\Facades\DB;

class TenantIsolationPolicy
{
    /**
     * Run the migrations.
     */
    public static function tenantIsolationUp(string $table): void
    {
        DB::statement('ALTER TABLE '.$table.' FORCE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE '.$table.' ENABLE ROW LEVEL SECURITY');

        DB::statement('CREATE POLICY tenant_isolation 
        ON '.$table.'
        FOR ALL 
        USING (tenant_id = NULLIF(current_setting(\'app.current_tenant_id\', TRUE), \'\')::uuid) 
        WITH CHECK (tenant_id = NULLIF(current_setting(\'app.current_tenant_id\', TRUE), \'\')::uuid)');
    }

    /**
     * Reverse the migrations.
     */
    public static function tenantIsolationDown(string $table): void
    {
        DB::statement('DROP POLICY IF EXISTS tenant_isolation ON '.$table);
        DB::statement('ALTER TABLE '.$table.'  DISABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE '.$table.'  NO FORCE ROW LEVEL SECURITY');
    }
}
