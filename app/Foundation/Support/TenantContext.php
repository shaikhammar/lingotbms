<?php

namespace App\Foundation\Support;

use App\Foundation\Exceptions\MissingTenantContextException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TenantContext
{
    /**
     * The explicit override slot.
     */
    protected static ?string $overrideTenantId = null;

    /**
     * Resolves the current tenant ID based on strict priority.
     */
    private static function resolve(): ?string
    {
        if (self::$overrideTenantId !== null) {
            return self::$overrideTenantId;
        }

        if (Auth::check() && Auth::user()->tenant_id !== null) {
            return (string) Auth::user()->tenant_id;
        }

        return null;
    }

    /**
     * Resolves the current tenant ID based on strict priority.
     */
    public static function getTenantId(): string
    {
        return self::resolve() ?? throw new MissingTenantContextException;
    }

    /**
     * Set the current tenant ID in the database session.
     *
     * @param  string  $tenantId  The tenant ID to set.
     * @param  bool  $transactionLocal  Whether the setting should be local to the current transaction.
     */
    private static function set(string $tenantId, bool $transactionLocal = false): void
    {
        DB::selectOne('SELECT set_config(\'app.current_tenant_id\', ?, ?)', [$tenantId, $transactionLocal ? 'true' : 'false']);
    }

    /**
     * Executes a callback under a specific tenant context and propagates it to Postgres.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function runFor(string $tenantId, callable $callback): mixed
    {
        // Capture the previous value, not just null, for safe nesting
        $previousId = self::$overrideTenantId;

        // Set the explicit override
        self::$overrideTenantId = $tenantId;

        try {
            // Let Laravel handle transaction management and savepoints
            return DB::transaction(function () use ($tenantId, $callback) {

                // Issue the transaction-local Postgres configuration
                self::set($tenantId, true);

                // Run the actual work
                return $callback();
            });
        } finally {
            // Restore the previous PHP override state
            self::$overrideTenantId = $previousId;

            // Restore the previous Postgres state if we are still inside an outer transaction.
            // (Because Laravel uses savepoints for nested transactions, the inner set_config
            // would otherwise bleed into the rest of the outer transaction once the savepoint is released).
            if (DB::transactionLevel() > 0) {
                try {
                    self::set(self::resolve() ?? '', true);
                } catch (\Throwable $e) {
                    // If the transaction has already been rolled back, we cannot set the config.
                    // This is a best-effort attempt to restore the previous state, but if the transaction
                    // has been rolled back, we cannot do anything about it.
                }
            }
        }
    }

    /**
     * Apply the tenant context for the duration of the current HTTP request.
     *
     * This sets the tenant ID in the database session for the request lifetime.
     */
    public static function applyForRequest(string $tenantId): void
    {
        self::set($tenantId, false);
    }
}
