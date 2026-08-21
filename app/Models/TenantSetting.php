<?php

namespace App\Models;

use App\Concerns\Foundation\BelongsToTenant;
use App\Support\Enum\CurrencyEnum;
use Database\Factories\TenantSettingFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class TenantSetting extends Model
{
    /** @use HasFactory<TenantSettingFactory> */
    use BelongsToTenant, HasFactory, HasUuids;

    protected $table = 'tenant_settings';

    protected $fillable = [
        'business_name',
        'address',
        'base_currency',
        'invoice_prefix',
        'invoice_next_number',
        'default_payment_terms_days',
        'timezone',
    ];

    protected $appends = [
        'logo_url',
    ];

    /**
     * Summary of casts
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoice_next_number' => 'integer',
            'default_payment_terms_days' => 'integer',
            'base_currency' => CurrencyEnum::class,
        ];
    }

    /**
     * Summary of logoUrl
     *
     * @return Attribute<string, string|null>
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::get(function () {

            if ($this->logo_path) {

                /** @var FilesystemAdapter $storage */
                $storage = Storage::disk('public');

                return $storage->url($this->logo_path);
            }

            return null;
        });
    }
}
