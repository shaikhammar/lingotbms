<?php

namespace App\Models;

use App\Concerns\Foundation\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['body', 'tenant_id'])]
class Note extends Model
{
    use BelongsToTenant, HasUuids;
}
