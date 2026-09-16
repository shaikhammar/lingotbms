<?php

use App\Modules\References\Enum\UnitEnum;

test('the same tenant cannot create two services with one name', function () {

    ['tenant' => $tenant] = actingAsTenant();

    $serviceData = [
        'name' => 'Translation',
        'default_unit' => UnitEnum::words->value,
    ];

    $this->put(route('settings.services.store'), $serviceData);

    $this->put(route('settings.services.store'), $serviceData)
        ->assertSessionHasErrors(['name']);
});

test('two different tenants can each own a service named Translation', function () {

    ['tenant' => $tenantA] = actingAsTenant();

    $serviceData = [
        'name' => 'Translation(words)',
        'default_unit' => UnitEnum::words->value,
    ];

    $this->put(route('settings.services.store'), $serviceData)
        ->assertSessionHasNoErrors();

    ['tenant' => $tenantB] = actingAsTenant();

    $this->put(route('settings.services.store'), $serviceData)
        ->assertSessionHasNoErrors();
});
