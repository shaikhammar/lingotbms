<?php

use App\Exceptions\MissingTenantContextException;
use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\Notes\Models\Note;
use App\Support\Contexts\TenantContext;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('fills tenant ID automatically from the authenticated user', function () {
    ['tenant' => $tenant, 'user' => $user] = actingAsTenant();

    $noteWithScope = Note::create([
        'body' => 'Test note',
    ]);

    $this->assertEquals($user->tenant_id, $noteWithScope->tenant_id);
});

test('fills tenant ID explicitly when creating a note', function () {
    ['tenant' => $tenant, 'user' => $user] = actingAsTenant();

    $noteWithExplicitTenantId = Note::create([
        'body' => 'Test note1',
        'tenant_id' => $user->tenant_id,
    ]);

    $this->assertEquals($user->tenant_id, $noteWithExplicitTenantId->tenant_id);
});

test('guest with explicit tenant_id can create a note', function () {

    $tenant = Tenant::factory()->create();

    TenantContext::applyForRequest($tenant->id);

    $noteWithExplicitTenantIdAsGuest = Note::create([
        'body' => 'Test note2',
        'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($tenant->id, $noteWithExplicitTenantIdAsGuest->tenant_id);
});

test('guest with explicit tenant_id cannot create a note due to row-level security', function () {

    $exception = expect(function () {
        $tenant = Tenant::factory()->create();
        $note = Note::create([
            'body' => 'Test note2',
            'tenant_id' => $tenant->id,
        ]);
    })->toThrow(QueryException::class, 'row-level security');
});

test('throws MissingTenantContextException when no authenticated user is present', function () {

    $this->assertEquals(0, DB::table('notes')->count());

    expect(function () {
        Note::create([
            'body' => 'Test note3',
        ]);
    })->toThrow(MissingTenantContextException::class);

    $this->assertEquals(0, DB::table('notes')->count());
});

test('two tenants can create notes without interfering with each other', function () {
    ['user' => $userA] = actingAsTenant();
    $noteA1 = Note::create([
        'body' => 'Tenant A User A note1',
    ]);
    $noteA2 = Note::create([
        'body' => 'Tenant A User A note2',
    ]);

    ['user' => $userB] = actingAsTenant();
    $noteB1 = Note::create([
        'body' => 'Tenant B User B note1',
    ]);
    $noteB2 = Note::create([
        'body' => 'Tenant B User B note2',
    ]);
    $noteB3 = Note::create([
        'body' => 'Tenant B User B note3',
    ]);

    $this->assertEquals(3, Note::count());
    $this->assertSame($userB->tenant_id, $noteB1->tenant_id);
    $this->assertSame($userB->tenant_id, $noteB2->tenant_id);
    $this->assertSame($userB->tenant_id, $noteB3->tenant_id);

    switchUser($userA);

    $this->assertEquals(2, Note::count());
    $this->assertSame($userA->tenant_id, $noteA1->tenant_id);
    $this->assertSame($userA->tenant_id, $noteA2->tenant_id);
});

test('guest queries on tenant-owned models are rejected', function () {

    expect(function () {
        Note::count();
    })->toThrow(MissingTenantContextException::class);
});

test('tenant isolation is enforced at the database level', function () {
    ['user' => $userA] = actingAsTenant();

    Note::create([
        'body' => 'Tenant A User A note1',
    ]);

    Note::create([
        'body' => 'Tenant A User A note2',
    ]);

    ['user' => $userB] = actingAsTenant();
    Note::create([
        'body' => 'Tenant B User B note1',
    ]);

    Note::create([
        'body' => 'Tenant B User B note2',
    ]);

    Note::create([
        'body' => 'Tenant B User B note3',
    ]);

    switchUser($userA);

    $this->assertCount(2, Note::withoutGlobalScope(TenantScope::class)->get());
    $this->assertDatabaseCount('notes', 2);
});

test('without tenant context no rows are visible', function () {

    ['user' => $userA] = actingAsTenant();

    Note::create([
        'body' => 'Tenant A User A note1',
    ]);

    Note::create([
        'body' => 'Tenant A User A note2',
    ]);

    ['user' => $userB] = actingAsTenant();
    Note::create([
        'body' => 'Tenant B User B note1',
    ]);

    Note::create([
        'body' => 'Tenant B User B note2',
    ]);

    Note::create([
        'body' => 'Tenant B User B note3',
    ]);

    // DB::select('RESET app.current_tenant_id');
    DB::select('SELECT NULLIF(set_config(\'app.current_tenant_id\', NULL, true), \'\')');
    $this->assertCount(0, Note::all());
});

test('mismatched tenant context cannot write another tenant\'s rows', function () {

    $exception = expect(function () {
        ['user' => $userA] = actingAsTenant();

        ['tenant' => $tenantB, 'user' => $userB] = actingAsTenant();

        switchUser($userA);

        Note::create([
            'body' => 'Test note2',
            'tenant_id' => $tenantB->id,
        ]);
    })->toThrow(QueryException::class, 'row-level security');
});

test('trait honours the override with no authenticated user', function () {
    $tenant = Tenant::factory()->create();

    $note = TenantContext::runFor($tenant->id, fn () => Note::create(['body' => 'Test note4']));

    expect($note->tenant_id)->toBe($tenant->id);
});

test('scope honours the override with no authenticated user', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    TenantContext::runFor($tenantA->id, fn () => Note::create(['body' => 'Test note1 for A']));
    TenantContext::runFor($tenantA->id, fn () => Note::create(['body' => 'Test note2 for A']));

    TenantContext::runFor($tenantB->id, fn () => Note::create(['body' => 'Test note1 for B']));

    TenantContext::runFor($tenantA->id, fn () => expect(Note::count())->toBe(2));
});

test('nesting restores both fences', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    runAsTenant($tenantA, fn () => Note::create(['body' => 'Test note1 for A']));
    runAsTenant($tenantA, fn () => Note::create(['body' => 'Test note2 for A']));

    runAsTenant($tenantB, fn () => Note::create(['body' => 'Test note1 for B']));

    TenantContext::runFor($tenantA->id, function () use ($tenantA, $tenantB) {
        expect(currentSettingTenantId())->toBe($tenantA->id);

        TenantContext::runFor($tenantB->id, function () use ($tenantB) {
            expect(currentSettingTenantId())->toBe($tenantB->id);
            expect(Note::withoutGlobalScope(TenantScope::class)->count())->toBe(1);
        });

        expect(currentSettingTenantId())->toBe($tenantA->id);
        expect(Note::withoutGlobalScope(TenantScope::class)->count())->toBe(2);
        expect(Note::count())->toBe(2);
    });

});

test('runFor restores the previous context when the inner callback throws', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    runAsTenant($tenantA, fn () => Note::create(['body' => 'a1']));

    TenantContext::runFor($tenantA->id, function () use ($tenantA, $tenantB) {
        try {
            TenantContext::runFor($tenantB->id, function () {
                Note::create(['body' => 'should not survive']);
                throw new RuntimeException('boom');
            });
        } catch (RuntimeException) {
            // swallowed on purpose
        }

        expect(currentSettingTenantId())->toBe($tenantA->id);
        expect(Note::count())->toBe(1);
    });
});

test('runFor restores context when the outermost callback throws', function () {
    $a = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $a->id]);

    runAsTenant($a, fn () => Note::create(['body' => 'a1']));

    try {
        TenantContext::runFor($a->id, fn () => throw new RuntimeException('boom'));
    } catch (RuntimeException) {
    }

    switchUser($user);
    expect(Note::count())->toBe(1);
});

test('update persists under a correct context', function () {
    ['user' => $userA] = actingAsTenant();

    $noteA1 = Note::create([
        'body' => 'Tenant A User A note1',
    ]);

    $noteA1->update(['body' => 'Updated note1']);

    expect($noteA1->fresh()->body)->toBe('Updated note1');
});

test('cross-tenant update affects zero rows', function () {
    ['tenant' => $tenantA] = actingAsTenant();

    $noteB1 = Note::create([
        'body' => 'Tenant B note1',
    ]);

    actingAsTenant();

    $affected = Note::withoutGlobalScopes()
        ->where('id', $noteB1->id)
        ->update(['body' => 'Updated note1']);

    expect($affected)->toBe(0);

    runAsTenant($tenantA, function () use ($noteB1) {
        expect(Note::find($noteB1->id)->body)->toBe('Tenant B note1');
    });
});

test('cross-tenant delete affects zero rows', function () {
    ['tenant' => $tenantA] = actingAsTenant();

    $noteB1 = Note::create([
        'body' => 'Tenant B note1',
    ]);

    actingAsTenant();

    $affected = Note::withoutGlobalScopes()
        ->where('id', $noteB1->id)
        ->delete();

    expect($affected)->toBe(0);

    runAsTenant($tenantA, function () use ($noteB1) {
        expect(Note::find($noteB1->id)->body)->toBe('Tenant B note1');
    });
});
