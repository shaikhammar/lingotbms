<?php

namespace App\Concerns\Foundation;

use App\Exceptions\MissingTenantContextException;
use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {

            if ($model->tenant_id) {
                return; // Skip setting tenant_id if the model already has a tenant_id
            } elseif (auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            } else {
                throw new MissingTenantContextException;
            }

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
