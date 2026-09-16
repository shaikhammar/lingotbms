<?php

use App\Foundation\Events\TenantCreated;
use App\Foundation\Models\Tenant;
use App\Modules\References\Models\Service;
use App\Modules\Settings\Models\TenantSetting;

test('a new tenant gets settings and services', function () {
    // TODO: create a tenant (the factory provisions it)
    // TODO: assert a settings row exists
    // TODO: assert the services count is greater than

    $tenant = Tenant::factory()->create();

    runAsTenant($tenant, function () use ($tenant) {
        $settings = TenantSetting::firstOrFail();
        expect($settings->count())->toBeGreaterThan(0);
        expect(Service::where('tenant_id', $tenant->id)->count())->toBeGreaterThan(0);
    });
});

test('a failure during provisioning leaves no tenant behind', function () {
    // TODO: make a listener throw. Options:
    //   - swap ServiceWriter in the container for one that throws
    //   - register an extra listener on TenantCreated that throws
    // TODO: attempt registration, expect the exception
    // TODO: assert Tenant::count() is UNCHANGED
    // TODO: assert the same for users, tenant_settings, services

    $this->withoutExceptionHandling();

    $initialTenantCount = Tenant::count();

    Event::listen(TenantCreated::class, function () {
        throw new Exception('Simulated failure during provisioning');
    });

    expect(fn () => $this->post(route('register'), [
        'name' => 'Test Tenant',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]))->toThrow(Exception::class, 'Simulated failure during provisioning');

    expect(Tenant::count())->toBe($initialTenantCount);

});
