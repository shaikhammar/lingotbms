<?php

use App\Foundation\Models\Tenant;
use App\Foundation\Models\User;
use App\Foundation\Support\TenantContext;
use App\Modules\Settings\Models\TenantSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Create a tenant and a user, log them in, and return both instances.
 *
 * @return array{tenant: Tenant, user: User}
 */
function actingAsTenant(?string $businessName = null): array
{
    // Fetching the tenant associated with the user
    /** @var Tenant $tenant */
    $tenant = Tenant::factory()->create();

    // Create the user belonging to that tenant
    /** @var User $user */
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    if ($businessName !== null) {
        runAsTenant($tenant, fn () => TenantSetting::query()->firstOrFail()->update([
            'business_name' => $businessName,
        ]));
    }
    // Authenticate the user
    switchUser($user);

    // Return both objects wrapped in a structured array
    return [
        'tenant' => $tenant,
        'user' => $user,
    ];
}

/**
 * Switch the authenticated user and update the tenant context.
 */
function switchUser(User $user): void
{
    test()->actingAs($user);
    TenantContext::applyForRequest($user->tenant_id);
}

/**
 * Run a callback under an explicit tenant context, with NO authenticated user.
 * The counterpart to actingAsTenant(): that one simulates a logged-in request,
 * this one simulates a seeder, a queued job, or registration.
 *
 * @template T
 *
 * @param  callable(): T  $callback
 * @return T
 */
function runAsTenant(Tenant $tenant, callable $callback): mixed
{
    return TenantContext::runFor($tenant->id, $callback);
}

/**
 * Read the tenant id Postgres currently believes it is scoped to.
 * Returns null if never set, '' if set to empty (see the runFor fallback).
 */
function currentSettingTenantId(): ?string
{
    return DB::selectOne(
        "SELECT current_setting('app.current_tenant_id', true) AS v"
    )->v;
}

/**
 * Returns a valid payload for tenant settings, optionally overridden by the provided array.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function validSettingsPayload(array $overrides = []): array
{
    return array_merge([
        'business_name' => 'Alpha Translations',
        'address' => '12 Example Street',
        'base_currency' => 'EUR',
        'invoice_prefix' => 'ALP',
        'invoice_next_number' => 42,
        'default_payment_terms_days' => 14,
        'timezone' => 'Europe/Berlin',
    ], $overrides);
}

/**
 * Summary of modules_on_disk
 */
function modules_on_disk(): array
{
    // Use __DIR__ to dynamically find the app directory, bypassing Laravel helpers.
    // Adjust the number of '/../' depending on how deep this test file is nested.
    // If this file is in tests/Feature/, you need '/../../app/Modules'
    $modulesPath = realpath(__DIR__.'/../app/Modules/');
    $modules = [];

    if ($modulesPath && is_dir($modulesPath)) {
        $moduleDirectories = scandir($modulesPath);
        foreach ($moduleDirectories as $module) {
            if ($module !== '.' && $module !== '..' && is_dir($modulesPath.'/'.$module)) {
                $modules[] = $module;
            }
        }
    }

    return $modules;
}
