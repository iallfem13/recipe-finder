<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['healthy', 'yummy', 'exotic', 'spicy', 'easy'];
        $rand = rand(0, 4);

        return [
            'name' => $categories[$rand],
            'slug' => $categories[$rand],
            'description' => $this->faker->paragraph(1, true)
        ];
    }
}
