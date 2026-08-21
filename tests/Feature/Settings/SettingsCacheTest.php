<?php

use App\Services\TenantSettingService;
use Illuminate\Http\UploadedFile;

test('second call performs no query', function () {
    actingAsTenant();

    $service = app(TenantSettingService::class);

    $service->shellPayload();

    $queriesCount = 0; // initialize queriescount

    DB::listen(function () use (&$queriesCount) {
        $queriesCount++;
    });

    $service->shellPayload();

    expect($queriesCount)->toBe(0);

});

test('two tenants produce two distinct keys', function () {
    $tenantA = actingAsTenant('Alpha')['tenant'];

    $service = app(TenantSettingService::class);

    $service->shellPayload();

    $tenantB = actingAsTenant('Beta')['tenant'];

    $service = app(TenantSettingService::class);

    $service->shellPayload();

    expect(Cache::get("tenant:{$tenantA->id}:settings")['business_name'])->toBe('Alpha')
        ->and(Cache::get("tenant:{$tenantB->id}:settings")['business_name'])->toBe('Beta');

});

test('cache does not leak between tenants', function () {
    $tenantA = actingAsTenant('Alpha')['tenant'];

    $service = app(TenantSettingService::class);

    $service->shellPayload();

    $tenantB = actingAsTenant('Beta')['tenant'];

    $service = app(TenantSettingService::class);

    $payload = $service->shellPayload();

    expect($payload['business_name'])->toBe('Beta');

});

test('updating settings clears the key', function () {
    $tenant = actingAsTenant('Alpha')['tenant'];

    $service = app(TenantSettingService::class);

    $key = "tenant:{$tenant->id}:settings";

    expect($service->shellPayload()['business_name'])->toBe('Alpha')
        ->and(Cache::has($key))->toBeTrue();

    $service->update(validSettingsPayload(['business_name' => 'Beta']));

    expect(Cache::missing($key))->toBeTrue()
        ->and($service->shellPayload()['business_name'])->toBe('Beta');

});

test('changing the logo clears the key', function () {
    Storage::fake('public');
    $tenant = actingAsTenant('Alpha Translations')['tenant'];

    $service = app(TenantSettingService::class);

    $key = "tenant:{$tenant->id}:settings";

    expect($service->shellPayload()['logo_url'])->toBeNull()
        ->and(Cache::has($key))->toBeTrue();

    $this->put(route('settings.business.update'), validSettingsPayload([
        'logo' => UploadedFile::fake()->image('logo.png'),
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(Cache::missing($key))->toBeTrue();

    $logoUrl = $service->shellPayload()['logo_url'];

    expect($logoUrl)->not()->toBeNull()
        ->and($logoUrl)->toContain("tenants/{$tenant->id}/");
});

test('cached payload contains only the shell fields', function () {
    $tenantA = actingAsTenant('Alpha')['tenant'];

    $service = app(TenantSettingService::class);

    $payload = $service->shellPayload();

    expect(array_keys($payload))->toBe([
        'business_name', 'logo_url', 'base_currency', 'timezone',
    ]);

});
