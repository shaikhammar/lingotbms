<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Support\Contexts\TenantContext;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
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
