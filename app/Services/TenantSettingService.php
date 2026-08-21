<?php

namespace App\Services;

use App\Models\TenantSetting;
use App\Support\Contexts\TenantContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantSettingService
{
    public function forCurrentTenant(): TenantSetting
    {
        $tenantId = TenantContext::getTenantId();

        return TenantSetting::firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'business_name' => null,
                'address' => null,
                'logo_path' => null,
                'base_currency' => 'USD',
                'invoice_prefix' => 'INV',
                'invoice_next_number' => 1,
                'default_payment_terms_days' => 30,
                'timezone' => 'UTC',
            ],
        );
    }

    /**
     * Update the tenant settings for the current tenant.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): TenantSetting
    {
        $tenantSetting = $this->forCurrentTenant();
        $tenantSetting->fill($data);

        $newLogo = $data['logo'] ?? null;

        $oldLogoPath = $tenantSetting->logo_path;

        $wantsToRemoveLogo = filter_var($data['remove_logo'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (($wantsToRemoveLogo || $newLogo) && $tenantSetting->logo_path) {
            // If there's an existing logo and no new logo is provided, keep the existing one
            $tenantSetting->logo_path = null;
        }

        if ($newLogo) {

            $tenantId = $tenantSetting->tenant_id;
            $random = Str::random(10);
            $ext = $newLogo->extension();
            $fileName = "logo-{$random}.{$ext}";
            $directory = "tenants/{$tenantId}";
            $tenantSetting->logo_path = $newLogo->storeAs($directory, $fileName, 'public');
        }

        $tenantSetting->save();
        Cache::forget("tenant:{$tenantSetting->tenant_id}:settings");

        if ($oldLogoPath && $oldLogoPath !== $tenantSetting->logo_path) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        return $tenantSetting;
    }

    /**
     * Get the shell payload for the current tenant.
     *
     * @return array<string, mixed>
     */
    public function shellPayload(): array
    {
        $tenantId = TenantContext::getTenantId();

        return Cache::remember("tenant:{$tenantId}:settings", now()->addMinutes(10), function () {
            $tenantSetting = $this->forCurrentTenant();

            return [
                'business_name' => $tenantSetting->business_name,
                'logo_url' => $tenantSetting->logo_url,
                'base_currency' => $tenantSetting->base_currency,
                'timezone' => $tenantSetting->timezone,
            ];
        });
    }
}
