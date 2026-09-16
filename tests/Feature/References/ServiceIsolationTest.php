<?php

use App\Modules\References\Enum\UnitEnum;
use App\Modules\References\Models\Service;
use Inertia\Testing\AssertableInertia;

use function PHPUnit\Framework\assertSame;

test('tenant B does not see tenant A services in the index', function () {

    ['tenant' => $tenantA] = actingAsTenant();

    $serviceA = Service::factory()->create([
        'name' => 'Tenant A Service',
        'default_unit' => UnitEnum::words->value,
    ]);

    ['tenant' => $tenantB] = actingAsTenant();

    $serviceB = Service::factory()->create([
        'name' => 'Tenant B Service',
        'default_unit' => UnitEnum::words->value,
    ]);

    $this->get(route('settings.services.index'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('settings/services/index')
            ->where('services', fn ($services) => $services
                ->contains('name', 'Tenant B Service')
                && ! $services->contains('name', 'Tenant A Service')));

});

test('tenant B gets a 404 on tenant A service', function () {

    ['tenant' => $tenantA] = actingAsTenant();

    $serviceA = Service::factory()->create([
        'name' => 'Tenant A Service',
        'default_unit' => UnitEnum::words->value,
    ]);

    ['tenant' => $tenantB] = actingAsTenant();

    $this->get(route('settings.services.edit', $serviceA->id))
        ->assertNotFound();
});

test('tenant B cannot update tenant A service', function () {

    ['tenant' => $tenantA] = actingAsTenant();

    $serviceA = Service::factory()->create([
        'name' => 'Tenant A Service',
        'default_unit' => UnitEnum::words->value,
    ]);

    ['tenant' => $tenantB] = actingAsTenant();

    $updateData = [
        'name' => 'Tenant B Service',
        'default_unit' => UnitEnum::hours->value,
    ];

    $this->patch(route('settings.services.update', $serviceA->id), $updateData)
        ->assertNotFound();

    runAsTenant($tenantA, function () use ($serviceA) {
        $this->assertDatabaseHas('services', [
            'id' => $serviceA->id,
            'name' => 'Tenant A Service',
            'default_unit' => UnitEnum::words->value,
        ]);
    });
});

test('Postgres itself refuses tenant A rows when the session is tenant B', function () {

    ['tenant' => $tenantA] = actingAsTenant();

    ['tenant' => $tenantB] = actingAsTenant();

    runAsTenant($tenantB, function () use ($tenantA, $tenantB) {
        assertSame($tenantB->id, currentSettingTenantId());

        $rows = DB::select('select tenant_id from services');

        foreach ($rows as $row) {
            assert($row->tenant_id !== $tenantA->id);
        }
    });
});
