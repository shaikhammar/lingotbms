<?php

use App\Concerns\Foundation\BelongsToTenant;

arch('All classes in the App namespace should be cased correctly')
    ->expect('App')
    ->toBeCasedCorrectly();

arch('Root Models should not use any modules')
    ->expect('App\Models')
    ->not
    ->toUse('App\Modules');

function modules_on_disk(): array
{
    // Use __DIR__ to dynamically find the app directory, bypassing Laravel helpers.
    // Adjust the number of '/../' depending on how deep this test file is nested.
    // If this file is in tests/Feature/, you need '/../../app/Modules'
    $modulesPath = realpath(__DIR__.'/../../app/Modules/');
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

$modules = modules_on_disk();
// Create the array of full namespaces (e.g., ['App\Modules\Sales', 'App\Modules\Finance'])
$allModuleNamespaces = array_map(fn ($module) => "App\\Modules\\{$module}", $modules);

foreach ($modules as $module) {
    arch("module {$module} is isolated")
        ->expect("App\\Modules\\{$module}")
        ->not->toUse(array_diff($allModuleNamespaces, ["App\\Modules\\{$module}"]));

    arch("module {$module} should use BelongsToTenant trait")
        ->expect("App\\Modules\\{$module}\\Models")
        ->toUse(BelongsToTenant::class);
}
