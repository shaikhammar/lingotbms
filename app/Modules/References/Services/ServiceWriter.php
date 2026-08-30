<?php

namespace App\Modules\References\Services;

use App\Modules\References\Models\Service;

class ServiceWriter
{
    private string $configKey = 'reference.default_services';

    public function provisionDefaults(): void
    {
        $defaultServices = config($this->configKey, []);
        foreach ($defaultServices as $code => $attrs) {
            Service::create($attrs);
        }
    }

    // public function create(array $data): Service
    // {
    //     // TODO: create
    //     // TODO: invalidate the tenant's services cache — IF you cache them (see decision 2)
    // }

    // public function update(Service $service, array $data): Service
    // {
    //     // TODO: update + invalidate
    // }

    // public function archive(Service $service): void
    // {
    //     // TODO: flip is_active. No delete method exists on this class, on purpose.
    // }

    // public function restore(Service $service): void
    // {
    //     // TODO
    // }
}
