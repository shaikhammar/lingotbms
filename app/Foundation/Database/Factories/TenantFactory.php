<?php

namespace App\Foundation\Database\Factories;

use App\Foundation\Models\Tenant;
use App\Foundation\Support\TenantContext;
use App\Modules\Settings\Models\TenantSetting;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
#[UseModel(Tenant::class)]
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Tenant $tenant) {

            TenantContext::runFor($tenant->id, function () {
                TenantSetting::create([
                    'business_name' => fake()->company(),
                ]);
            });
        });
    }
}
