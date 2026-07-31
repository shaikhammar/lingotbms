<?php

namespace App\Support\Contexts;

use Illuminate\Support\Facades\DB;

class TenantContext
{
    /**
     * Set the current tenant ID in the database session.
     *
     * @param  string  $tenantId  The tenant ID to set.
     * @param  bool  $transactionLocal  Whether the setting should be local to the current transaction.
     */
    public static function set(string $tenantId, bool $transactionLocal = false): void
    {
        DB::select('SELECT set_config(\'app.current_tenant_id\', ?, ?)', [$tenantId, $transactionLocal]);
    }
}
