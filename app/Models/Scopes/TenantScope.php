<?php

namespace App\Models\Scopes;

use App\Exceptions\MissingTenantContextException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Class TenantScope
 *
 * @implements Scope<Model>
 */
class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check()) {
            $builder->where($model->qualifyColumn('tenant_id'), auth()->user()->tenant_id);
        } else {
            throw new MissingTenantContextException;
        }
    }
}
