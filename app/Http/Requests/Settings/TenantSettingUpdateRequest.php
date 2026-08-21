<?php

namespace App\Http\Requests\Settings;

use App\Support\Enum\CurrencyEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantSettingUpdateRequest extends FormRequest
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
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'base_currency' => ['required', 'string', 'size:3', Rule::enum(CurrencyEnum::class)],
            'invoice_prefix' => ['nullable', 'string', 'max:10', 'alpha', 'uppercase'],
            'invoice_next_number' => ['required', 'integer', 'min:1'],
            'default_payment_terms_days' => ['required', 'integer', 'min:0', 'max:365'],
            'timezone' => ['required', 'string', 'timezone:all'],
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg', Rule::dimensions(['max_width' => 10000, 'max_height' => 10000])],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'remove_logo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'base_currency.size' => 'The base currency must be a 3-letter ISO code.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'base_currency' => 'base currency',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('invoice_prefix')) {
            $this->merge([
                'invoice_prefix' => trim($this->input('invoice_prefix')),
            ]);
        }

        if ($this->has('base_currency')) {
            $this->merge([
                'base_currency' => strtoupper($this->input('base_currency')),
            ]);
        }

    }
}
