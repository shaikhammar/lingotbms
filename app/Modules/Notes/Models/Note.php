<?php

namespace App\Modules\Notes\Models;

use App\Foundation\Concerns\BelongsToTenant;
use App\Foundation\Models\Tenant;
use App\Modules\Notes\Database\Factories\NoteFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $body
 * @property string $tenant_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Tenant|null $tenant
 *
 * @method static \App\Modules\Notes\Database\Factories\NoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['body', 'tenant_id'])]
#[UseFactory(NoteFactory::class)]
class Note extends Model
{
    /**
     * @use HasFactory<NoteFactory>
     */
    use BelongsToTenant, HasFactory, HasUuids;
}
