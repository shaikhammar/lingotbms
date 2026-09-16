<?php

namespace App\Modules\Settings\Database\Factories;

use App\Modules\Settings\Models\TenantSetting;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantSetting>
 */
#[UseModel(TenantSetting::class)]
class TenantSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'logo_path' => $this->faker->imageUrl(),
            'base_currency' => 'USD',
            'invoice_prefix' => 'INV',
            'invoice_next_number' => 1,
            'default_payment_terms_days' => 30,
            'timezone' => 'UTC',
        ];
    }
}
