<?php

use App\Modules\Notes\Providers\NotesServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    NotesServiceProvider::class,
    AppServiceProvider::class,
    FortifyServiceProvider::class,
];
