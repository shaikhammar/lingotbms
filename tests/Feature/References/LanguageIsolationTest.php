<?php

use App\Modules\References\Models\Language;
use Database\Seeders\LanguageSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;

test('languages are readable by any tenant and carry no tenant fence', function () {

    $this->seed(LanguageSeeder::class);

    ['tenant' => $a] = actingAsTenant();

    assertSame(currentSettingTenantId(), $a->id);

    $aCount = Language::count();

    ['tenant' => $b] = actingAsTenant();

    assertSame(currentSettingTenantId(), $b->id);

    $bCount = Language::count();

    assertNotSame($a->id, $b->id);

    assertGreaterThan(0, $aCount);
    assertGreaterThan(0, $bCount);

    assertEquals($aCount, $bCount);

    assertFalse(Schema::hasColumn('languages', 'tenant_id'));

    $policies = DB::selectOne('select count(*) as count from pg_policies where tablename = ?', ['languages']);

    assertEquals(0, $policies->count);

});
