<?php

use App\Models\Tenant;
use App\Models\User;
use App\Support\Contexts\TenantContext;
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

function something()
{
    // ..
}

/**
 * Create a tenant and a user, log them in, and return both instances.
 *
 * @return array{tenant: Tenant, user: User}
 */
function actingAsTenant(): array
{
    // Fetching the tenant associated with the user
    /** @var Tenant $tenant */
    $tenant = Tenant::factory()->create();

    // Create the user belonging to that tenant
    /** @var User $user */
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

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
    TenantContext::set($user->tenant_id, true);
}
