<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\References\Enum\CurrencyEnum;
use App\Modules\Settings\Http\Requests\TenantSettingUpdateRequest;
use App\Modules\Settings\Services\TenantSettingService;
use DateTimeZone;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TenantSettingController extends Controller
{
    public function edit(TenantSettingService $tenantSettingService): Response
    {

        return Inertia::render('settings/business', [
            // Pass any necessary data to the view
            'tenantSetting' => $tenantSettingService->forCurrentTenant(),
            'currencies' => CurrencyEnum::cases(),
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(TenantSettingUpdateRequest $request, TenantSettingService $tenantSettingService): RedirectResponse
    {
        // Handle business settings update logic here
        $tenantSettingService->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Business settings updated successfully.',
        ]);

        return back();
    }
}
