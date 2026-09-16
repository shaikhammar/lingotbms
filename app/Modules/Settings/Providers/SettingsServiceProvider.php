<?php

namespace App\Modules\Settings\Providers;

use App\Foundation\Events\TenantCreated;
use App\Modules\Settings\Listeners\CreateDefaultSettings;
use App\Providers\BaseModuleServiceProvider;
use Illuminate\Support\Facades\Event;

class SettingsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        Event::listen(TenantCreated::class, CreateDefaultSettings::class);
    }
}
