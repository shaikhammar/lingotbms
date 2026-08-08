<?php

namespace App\Modules\Notes\Models;

use App\Concerns\Foundation\BelongsToTenant;
use App\Modules\Notes\Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $body
 * @property string $tenant_id
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
