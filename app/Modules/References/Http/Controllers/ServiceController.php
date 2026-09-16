<?php

namespace App\Modules\References\Http\Controllers;

use App\Modules\References\Enum\UnitEnum;
use App\Modules\References\Http\Requests\ServiceRequest;
use App\Modules\References\Models\Service;
use App\Modules\References\Services\ServiceWriter;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('settings/services/index', [
            'services' => Service::get(),   // TODO: Replace with ServiceRepository
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('settings/services/create', [
            'units' => UnitEnum::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequest $request, ServiceWriter $serviceWriter): RedirectResponse
    {
        $serviceWriter->create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Service created successfully.',
        ]);

        return redirect(route('settings.services.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): Response
    {
        return Inertia::render('settings/services/edit', [
            'service' => $service,
            'units' => UnitEnum::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequest $request, Service $service, ServiceWriter $serviceWriter): RedirectResponse
    {
        $serviceWriter->update($service, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Service updated successfully.',
        ]);

        return redirect(route('settings.services.index'));
    }

    /**
     * Archive the service
     */
    public function archive(Service $service, ServiceWriter $serviceWriter): void
    {
        $serviceWriter->archive($service);
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Service archived successfully.',
        ]);

    }

    /**
     * Restore the service
     */
    public function restore(Service $service, ServiceWriter $serviceWriter): void
    {
        $serviceWriter->restore($service);
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Service restored successfully.',
        ]);

    }
}
