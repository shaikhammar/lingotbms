<?php

use Illuminate\Support\Facades\DB;

test('every tenant-owned table has RLS enabled with full policy coverage', function () {
    // Tables that legitimately carry tenant_id without RLS.
    // Adding to this list must be a deliberate decision, visible in a diff.
    $exempt = [
        // Auth lookups (login, password reset, session user resolution) run
        // before tenant context exists; a policy here breaks authentication.
        'users',
    ];

    $rows = DB::select(<<<'SQL'
        SELECT
            c.relname                                        AS table_name,
            c.relrowsecurity::int                            AS rls_enabled,
            c.relforcerowsecurity::int                       AS rls_forced,
            COALESCE(
                string_agg(DISTINCT p.polcmd::text, '' ORDER BY p.polcmd::text),
                ''
            )                                                AS commands
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        JOIN pg_attribute a
              ON a.attrelid = c.oid
             AND a.attname  = 'tenant_id'
             AND a.attnum   > 0
             AND NOT a.attisdropped
        LEFT JOIN pg_policy p ON p.polrelid = c.oid
        WHERE n.nspname = 'public'
          AND c.relkind = 'r'
        GROUP BY c.relname, c.relrowsecurity, c.relforcerowsecurity
        ORDER BY c.relname
    SQL);

    expect($rows)->not->toBeEmpty('No tables with a tenant_id column were found. The inventory query is wrong.');

    $labels = ['r' => 'SELECT', 'a' => 'INSERT', 'w' => 'UPDATE', 'd' => 'DELETE'];
    $problems = [];

    foreach ($rows as $row) {
        if (in_array($row->table_name, $exempt, true)) {
            continue;
        }

        if ((int) $row->rls_enabled !== 1) {
            $problems[] = "{$row->table_name}: RLS is not enabled";

            continue;
        }

        if ((int) $row->rls_forced !== 1) {
            $problems[] = "{$row->table_name}: RLS is not FORCED (the table owner bypasses it)";
        }

        if ($row->commands === '') {
            $problems[] = "{$row->table_name}: RLS enabled but no policy exists";

            continue;
        }

        // '*' means FOR ALL. Otherwise every command needs its own policy.
        if (! str_contains($row->commands, '*')) {
            $missing = array_diff(['r', 'a', 'w', 'd'], str_split($row->commands));

            if ($missing !== []) {
                $names = implode(', ', array_map(fn ($c) => $labels[$c], $missing));
                $problems[] = "{$row->table_name}: no policy for {$names}";
            }
        }
    }

    $this->assertSame([], $problems,
        "Tenant-owned tables missing protection:\n- ".implode("\n- ", $problems)
    );
});
