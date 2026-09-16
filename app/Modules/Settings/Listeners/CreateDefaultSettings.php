<?php

namespace App\Modules\Settings\Listeners;

use App\Modules\Settings\Services\TenantSettingService;

class CreateDefaultSettings
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private TenantSettingService $tenantSettingService
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $this->tenantSettingService->forCurrentTenant();
    }
}
