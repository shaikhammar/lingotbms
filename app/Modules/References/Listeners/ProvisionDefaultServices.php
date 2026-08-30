<?php

namespace App\Modules\References\Listeners;

use App\Foundation\Events\TenantCreated;
use App\Modules\References\Services\ServiceWriter;

class ProvisionDefaultServices
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private ServiceWriter $serviceWriter
    ) {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TenantCreated $event): void
    {
        $this->serviceWriter->provisionDefaults();
    }
}
