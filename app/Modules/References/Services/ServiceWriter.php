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

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Service
    {
        $service = Service::firstOrCreate($data);

        return $service;
    }

    /**
     * Summary of update
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Service $service, array $data): Service
    {
        $service->fill($data);
        $service->save();

        return $service;
    }

    public function archive(Service $service): void
    {
        $service->is_active = ! $service->is_active;
        $service->save();
    }

    public function restore(Service $service): void
    {
        $service->is_active = ! $service->is_active;
        $service->save();
    }
}
