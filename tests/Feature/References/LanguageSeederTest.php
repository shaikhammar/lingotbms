<?php

use App\Modules\References\Models\Language;
use Database\Seeders\LanguageSeeder;

use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertGreaterThan;

test('language seeder can run twice without duplicating', function () {

    $this->seed(LanguageSeeder::class);

    $initialCount = Language::count();

    $this->seed(LanguageSeeder::class);

    assertGreaterThan(0, $initialCount);

    assertEquals($initialCount, Language::count());
});

test('language seeder restores a deleted row', function () {
    $this->seed(LanguageSeeder::class);

    $language = Language::first();

    $code = $language->code;

    $language->delete();

    $this->seed(LanguageSeeder::class);

    assertDatabaseHas('languages', ['code' => $code]);

});
