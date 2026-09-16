<?php

namespace App\Modules\References\Providers;

use App\Foundation\Events\TenantCreated;
use App\Modules\References\Listeners\ProvisionDefaultServices;
use App\Providers\BaseModuleServiceProvider;
use Illuminate\Support\Facades\Event;

class ReferencesServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();

        Event::listen(TenantCreated::class, ProvisionDefaultServices::class);
    }
}
