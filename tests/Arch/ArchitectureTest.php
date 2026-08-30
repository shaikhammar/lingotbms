<?php

use App\Foundation\Database\Factories\TenantFactory;

$core = ['Foundation'];

$shared = ['References'];

$modules = modules_on_disk();

$featureModules = array_diff($modules, $shared);

// TODO: Refer to the arch()->preset()->laravel() and implement a similar arch tests

arch('all classes in the App namespace should be cased correctly')
    ->expect('App')
    ->toBeCasedCorrectly();

$foundationNamespace = 'App\Foundation';

$moduleNamespace = 'App\Modules';

arch('foundation does not use modules')
    ->expect($foundationNamespace)
    ->not
    ->toUse($moduleNamespace)
    ->ignoring(TenantFactory::class);       // TODO change this to App\Foundation and remove this line

$allModuleNamespaces = array_map(fn ($module) => "App\\Modules\\{$module}", $modules);

$featureNamespaces = array_map(fn ($featureModule) => "App\\Modules\\{$featureModule}", $featureModules);
foreach ($shared as $sharedModule) {
    arch("shared module {$sharedModule} does not use feature modules")
        ->expect("App\\Modules\\{$sharedModule}")
        ->not()->toUse(array_diff($allModuleNamespaces, ["App\\Modules\\{$sharedModule}"]));
}

foreach ($featureModules as $featureModule) {
    arch("feature module {$featureModule} is isolated")
        ->expect("App\\Modules\\{$featureModule}")
        ->not->toUse(array_diff($featureNamespaces, ["App\\Modules\\{$featureModule}"]));

}
