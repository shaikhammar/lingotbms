<?php

test('guest pages render without tenant context', function (string $route) {
    $this->get(route($route))->assertOk()
        ->assertInertia(fn ($page) => $page->where('tenantSettings', null));
})->with([
    'login' => ['login'],
    'register' => ['register'],
    'password request' => ['password.request'],
]);

test('authenticated response carries the settings shared prop', function () {
    actingAsTenant('Alpha Translations');

    $this->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page
            ->where('tenantSettings.business_name', 'Alpha Translations')
            ->has('tenantSettings.logo_url')
            ->has('tenantSettings.base_currency')
            ->has('tenantSettings.timezone')
            ->etc()
        );
});

test('shared prop reflects a change immediately after save', function () {
    $tenant = actingAsTenant('Alpha Translations')['tenant'];

    $this->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page->where('tenantSettings.business_name', 'Alpha Translations')->etc());

    $this->put(route('settings.business.update'), validSettingsPayload([
        'business_name' => 'Beta Localisation',
    ]))->assertRedirect();

    $this->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page->where('tenantSettings.business_name', 'Beta Localisation')->etc());
});
