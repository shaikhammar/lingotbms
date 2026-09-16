<?php

namespace App\Modules\References\Models;

use App\Foundation\Concerns\BelongsToTenant;
use App\Foundation\Models\Tenant;
use App\Modules\References\Database\Factories\ServiceFactory;
use App\Modules\References\Enum\UnitEnum;
use Carbon\CarbonImmutable;
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
 * @property string $id
 * @property string|null $tenant_id
 * @property string $name
 * @property string|null $code
 * @property UnitEnum $default_unit
 * @property bool $is_active
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static Builder<static>|Service active()
 * @method static \App\Modules\References\Database\Factories\ServiceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Service whereCode($value)
 * @method static Builder<static>|Service whereCreatedAt($value)
 * @method static Builder<static>|Service whereDefaultUnit($value)
 * @method static Builder<static>|Service whereId($value)
 * @method static Builder<static>|Service whereIsActive($value)
 * @method static Builder<static>|Service whereName($value)
 * @method static Builder<static>|Service whereTenantId($value)
 * @method static Builder<static>|Service whereUpdatedAt($value)
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
