<?php

namespace App\Foundation\Concerns;

use App\Foundation\Models\Tenant;
use App\Foundation\Scopes\TenantScope;
use App\Foundation\Support\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {

            $model->tenant_id ??= TenantContext::getTenantId();
        });
    }

    /**
     * Get the tenant that owns the model.
     *
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
