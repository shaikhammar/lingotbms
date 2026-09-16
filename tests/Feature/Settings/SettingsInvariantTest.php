<?php

use App\Actions\Fortify\CreateNewUser;
use App\Foundation\Models\Tenant;
use App\Foundation\Models\User;
use App\Modules\References\Enum\CurrencyEnum;
use App\Modules\Settings\Models\TenantSetting;
use Illuminate\Database\QueryException;

test('registration creates a tenant, its settings, and the user', function () {
    $response = $this->post('/register', [
        'name' => 'Ammar Translations',
        'email' => 'ammar@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticated();

    $this->assertDatabaseCount('tenants', 1);
    $this->assertDatabaseCount('users', 1);

    $tenant = Tenant::firstOrFail();
    $user = User::firstOrFail();

    expect($user->tenant_id)->toBe($tenant->id);

    runAsTenant($tenant, function () use ($tenant) {
        $settings = TenantSetting::firstOrFail();

        expect($settings->tenant_id)->toBe($tenant->id)
            ->and($settings->business_name)->toBeNull()
            ->and($settings->base_currency)->toBe(CurrencyEnum::USD)
            ->and($settings->invoice_prefix)->toBe('INV')
            ->and($settings->invoice_next_number)->toBe(1)
            ->and($settings->default_payment_terms_days)->toBe(30)
            ->and($settings->timezone)->toBe('UTC');
    });
});

test('a failure after tenant creation leaves no orphan tenant', function () {
    TenantSetting::creating(fn () => throw new RuntimeException('boom'));

    expect(fn () => app(CreateNewUser::class)->create([
        'name' => 'Ammar Translations',
        'email' => 'ammar@example.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]))->toThrow(RuntimeException::class);

    $this->assertDatabaseCount('tenants', 0);
    $this->assertDatabaseCount('users', 0);
});

test('a tenant cannot have two settings rows', function () {
    $tenant = Tenant::factory()->create();

    // runAsTenant($tenant, fn () => TenantSetting::update(['business_name' => 'First']));

    expect(fn () => runAsTenant($tenant, fn () => TenantSetting::create(['business_name' => 'Second'])))
        ->toThrow(QueryException::class, 'tenant_settings_tenant_id_unique');
});
