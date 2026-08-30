<?php

use App\Modules\Settings\Models\TenantSetting;

test('guest is redirected from the edit page', function () {
    $this->get(route('settings.business.edit'))
        ->assertRedirect(route('login'));
});

test('authenticated user gets the right component, props and current tenant\'s values', function () {
    $tenantA = actingAsTenant()['tenant'];
    runAsTenant($tenantA, fn () => TenantSetting::query()->firstOrFail()->update([
        'business_name' => 'Tenant A Business',
    ]));
    $tenantB = actingAsTenant()['tenant'];
    runAsTenant($tenantB, fn () => TenantSetting::query()->firstOrFail()->update([
        'business_name' => 'Tenant B Business',
    ]));

    $this->get(route('settings.business.edit'))
        ->assertInertia(fn ($page) => $page
            ->component('settings/business')
            ->has('tenantSetting')
            ->has('currencies')
            ->has('timezones')
            ->where('tenantSetting.business_name', 'Tenant B Business')
            ->etc()
        );
});
