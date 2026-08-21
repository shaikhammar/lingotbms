<?php

use App\Modules\Notes\Providers\NotesServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\TenantSettingsServiceProvider;

return [
    NotesServiceProvider::class,
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    TenantSettingsServiceProvider::class,
];
