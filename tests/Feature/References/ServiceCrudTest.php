<?php

use App\Modules\References\Enum\UnitEnum;
use App\Modules\References\Models\Service;
use Inertia\Testing\AssertableInertia;

test('guest cannot reach the services screen', function () {
    $this->get(route('settings.services.index'))
        ->assertRedirect(route('login'));
});

test('the index renders the right component with the right props', function () {

    ['tenant' => $tenant] = actingAsTenant();

    $service = Service::factory()->create([
        'name' => 'Original Name',
        'default_unit' => UnitEnum::words->value,
    ]);

    $this->get(route('settings.services.index'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('settings/services/index')
            ->has('services', fn (AssertableInertia $page) => $page
                ->whereContains('name', 'Original Name')
                ->whereContains('default_unit', UnitEnum::words->value)
                ->etc()
            ));
});

test('a valid update redirects, flashes, and changes the row', function () {

    ['tenant' => $tenant] = actingAsTenant();

    $updateData = [
        'name' => 'Updated Name',
        'default_unit' => UnitEnum::hours->value,
    ];

    $service = Service::factory()->create([
        'name' => 'Original Name',
        'default_unit' => UnitEnum::words->value,
    ]);

    $this->patch(route('settings.services.update', $service->id), $updateData)
        ->assertRedirect(route('settings.services.index'))
        ->assertSessionHas('inertia.flash_data', fn ($flash) => $flash['toast']['type'] === 'success');
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Updated Name',
        'default_unit' => UnitEnum::hours->value,
    ]);

});

test('an invalid update returns errors and changes nothing', function () {

    ['tenant' => $tenant] = actingAsTenant();

    $updateData = [
        'name' => '',
        'default_unit' => UnitEnum::hours->value,
    ];

    $service = Service::factory()->create([
        'name' => 'Original Name',
        'default_unit' => UnitEnum::words->value,
    ]);

    $this->patch(route('settings.services.update', $service->id), $updateData)
        ->assertSessionHasErrors(['name']);
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Original Name',
        'default_unit' => UnitEnum::words->value,
    ]);
});
