<?php

namespace Database\Seeders;

use App\Modules\References\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languagesPath = database_path('data/languages.json');

        if (file_exists($languagesPath)) {

            $fileContent = file_get_contents($languagesPath);

            if (is_string($fileContent)) {

                $langauges = json_decode($fileContent, true);

                if (! empty($langauges)) {
                    Language::upsert($langauges, ['code'], ['name', 'native_name']);
                }
            }
        }

    }
}
