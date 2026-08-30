<?php

namespace App\Modules\References\Models;

use App\Foundation\Concerns\BelongsToTenant;
use App\Foundation\Models\Tenant;
use App\Modules\References\Database\Factories\ServiceFactory;
use App\Modules\References\Enum\UnitEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read Tenant|null $tenant
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 *
 * @mixin \Eloquent
 */
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use BelongsToTenant, HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'default_unit',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_unit' => UnitEnum::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return ServiceFactory
     */
    protected static function newFactory()
    {
        return ServiceFactory::new();
    }

    /**
     * only active services
     *
     * @param  Builder<static>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', 1);
    }
}
