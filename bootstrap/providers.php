<?php

use App\Modules\Notes\Providers\NotesServiceProvider;
use App\Modules\References\Providers\ReferencesServiceProvider;
use App\Modules\Settings\Providers\SettingsServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\BaseModuleServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    NotesServiceProvider::class,
    SettingsServiceProvider::class,
    AppServiceProvider::class,
    BaseModuleServiceProvider::class,
    FortifyServiceProvider::class,
    ReferencesServiceProvider::class,
];
