<?php

namespace App\Modules\Settings\Models;

use App\Foundation\Concerns\BelongsToTenant;
use App\Foundation\Models\Tenant;
use App\Modules\References\Enum\CurrencyEnum;
use App\Modules\Settings\Database\Factories\TenantSettingFactory;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string|null $business_name
 * @property string|null $address
 * @property string|null $logo_path
 * @property CurrencyEnum $base_currency
 * @property string $invoice_prefix
 * @property int $invoice_next_number
 * @property int $default_payment_terms_days
 * @property string $timezone
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read mixed $logo_url
 * @property-read Tenant $tenant
 *
 * @method static \App\Modules\Settings\Database\Factories\TenantSettingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereBaseCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereDefaultPaymentTermsDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereInvoiceNextNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereInvoicePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereLogoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereTenantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TenantSetting whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
     * @return TenantSettingFactory
     */
    protected static function newFactory()
    {
        return TenantSettingFactory::new();
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
