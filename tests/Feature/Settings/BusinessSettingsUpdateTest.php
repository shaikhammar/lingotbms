<?php

use App\Foundation\Models\Tenant;
use App\Modules\References\Enum\CurrencyEnum;
use App\Modules\Settings\Models\TenantSetting;

test('valid update persists every field', function () {
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), [
        'business_name' => 'Alpha Translations',
        'address' => '12 Example Street',
        'base_currency' => 'EUR',
        'invoice_prefix' => 'ALP',
        'invoice_next_number' => 42,
        'default_payment_terms_days' => 14,
        'timezone' => 'Europe/Berlin',
    ]);

    $settings = TenantSetting::firstOrFail();   // re-read, do not trust an in-memory model
    expect($settings->business_name)->toBe('Alpha Translations')
        ->and($settings->address)->toBe('12 Example Street')
        ->and($settings->base_currency)->toBe(CurrencyEnum::EUR)
        ->and($settings->invoice_prefix)->toBe('ALP')
        ->and($settings->invoice_next_number)->toBe(42)
        ->and($settings->default_payment_terms_days)->toBe(14)
        ->and($settings->timezone)->toBe('Europe/Berlin');
});

test('normalisation applies', function () {
    $tenant = actingAsTenant()['tenant'];

    $this->put(route('settings.business.update'), [
        'business_name' => 'Alpha Translations',
        'address' => '12 Example Street',
        'base_currency' => 'eur',
        'invoice_prefix' => 'ALP',
        'invoice_next_number' => 42,
        'default_payment_terms_days' => 14,
        'timezone' => 'Europe/Berlin',
    ]);

    $settings = TenantSetting::firstOrFail();   // re-read, do not trust an in-memory model
    expect($settings->business_name)->toBe('Alpha Translations')
        ->and($settings->address)->toBe('12 Example Street')
        ->and($settings->base_currency)->toBe(CurrencyEnum::EUR)
        ->and($settings->invoice_prefix)->toBe('ALP')
        ->and($settings->invoice_next_number)->toBe(42)
        ->and($settings->default_payment_terms_days)->toBe(14)
        ->and($settings->timezone)->toBe('Europe/Berlin');
});

test('validation failures leave the row unchanged', function () {
    $tenant = actingAsTenant()['tenant'];
    $before = TenantSetting::query()->firstOrFail()->toArray();

    $this->put(route('settings.business.update'), validSettingsPayload([
        'business_name' => 'Should Not Be Saved',
        'base_currency' => 'XYZ', // invalid currency
    ]))
        ->assertSessionHasErrors('base_currency');
    // ->assertSessionHasNoErrors();

    expect(TenantSetting::query()->firstOrFail()->toArray())->toBe($before);
});

test('non-input columns cannot be mass-assigned', function () {
    ['tenant' => $tenant] = actingAsTenant();
    $otherTenant = Tenant::factory()->create();

    $originalLogoPath = TenantSetting::firstOrFail()->logo_path;

    $this->put(route('settings.business.update'), validSettingsPayload([
        'tenant_id' => $tenant->id,
        'logo_path' => 'malicious.php',
    ]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $settings = TenantSetting::firstOrFail();   // re-read, do not trust an in-memory model

    expect($settings->tenant_id)->toBe($tenant->id)
        ->and($settings->logo_path)->toBe($originalLogoPath)
        ->and($settings->business_name)->toBe('Alpha Translations');
});

test('update touches only the acting tenant\'s row', function () {
    $tenantA = actingAsTenant()['tenant'];
    runAsTenant($tenantA, fn () => TenantSetting::query()->firstOrFail()->update(['business_name' => 'Alpha']));

    actingAsTenant();   // now B
    $this->put(route('settings.business.update'), validSettingsPayload([
        'business_name' => 'Beta',
    ]));

    runAsTenant($tenantA, function () {
        expect(TenantSetting::firstOrFail()->business_name)->toBe('Alpha');
    });
});

test('validation rejects invalid input', function (string $field, mixed $value) {
    actingAsTenant();

    $this->put(route('settings.business.update'), validSettingsPayload([$field => $value]))
        ->assertSessionHasErrors($field);
})->with([
    'blank business name' => ['business_name', ''],
    'business name too long' => ['business_name', str_repeat('a', 256)],
    'unknown currency' => ['base_currency', 'XYZ'],
    'currency wrong length' => ['base_currency', 'EURO'],
    'invalid timezone' => ['timezone', 'Mars/Olympus'],
    'blank timezone' => ['timezone', ''],
    'invoice number zero' => ['invoice_next_number', 0],
    'invoice number negative' => ['invoice_next_number', -5],
    'invoice number not int' => ['invoice_next_number', 'abc'],
    'payment terms negative' => ['default_payment_terms_days', -1],
    'payment terms too large' => ['default_payment_terms_days', 400],
    'prefix illegal chars' => ['invoice_prefix', 'A/B<>'],
]);
