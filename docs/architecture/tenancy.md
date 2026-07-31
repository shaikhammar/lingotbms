- ## Fence 1 
    Eloquent global scope. Every query on a tenant-owned model automatically gets WHERE tenant_id = <current tenant> appended by Laravel. This is the ergonomic fence: you never write the WHERE yourself, so you can never forget it.
    
- ## Fence 2 
    Postgres RLS. The database itself refuses to return or accept rows whose tenant_id doesn't match a per-connection session variable the app sets at the start of each request. This fence holds even if Fence 1 fails — a forgotten scope, a raw DB::select, a future bug, a compromised query.