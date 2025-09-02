<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst($this->faker->unique()->words(4, true));

        $instructions = [];
        for ($i = 1; $i <= rand(1,8); $i++) {
            $instructions[$i] = $this->faker->sentence;
        }

        return [
            'title' => $title,
            'slug' => \Str::slug($title),
            'servings' => rand(1, 5),
            'cook_time' => rand(30, 90),
            'user_id' => User::factory(),
            'image_url' => '/recipe_images/recipe_' . rand(1, 3) . '.jpg',
            'description' => $this->faker->paragraph(3, true),
            'instructions' => $instructions
        ];
    }
}
