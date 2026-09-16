<?php

namespace App\Modules\References\Http\Requests;

use App\Foundation\Support\TenantContext;
use App\Modules\References\Enum\UnitEnum;
use App\Modules\References\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $tenantId = TenantContext::getTenantId();

        return [
            'name' => ['required', 'string', 'max:100',
                Rule::unique(Service::class, 'name')
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId))
                    ->ignore($this->route('settings.service.create')),
            ],
            'code' => ['string', 'nullable'],
            'default_unit' => ['required', Rule::enum(UnitEnum::class)],
        ];
    }
}
