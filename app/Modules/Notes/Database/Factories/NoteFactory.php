<?php

namespace App\Modules\Notes\Database\Factories;

use App\Foundation\Models\Tenant;
use App\Modules\Notes\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => $this->faker->sentence(),
            'tenant_id' => Tenant::factory(),
        ];
    }
}
