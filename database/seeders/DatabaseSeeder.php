<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // generate ingredients
        $ingredients = Ingredient::factory(20)->create();

        // create users (authors)
        $users = User::factory(6)->create();

        // add random presentation
        $presentation = ['choped', 'minced', 'crushed'];

        // attach users to recipes
        foreach ($users as $user) {
            Recipe::factory(15)->create([
                'user_id' => $user->id
            ])->each(function ($recipe) use ($ingredients, $presentation) {
                $recipeIngredients = $ingredients->random(rand(5, 10));

                foreach ($recipeIngredients as $ingredient) {
                    $recipe->ingredients()->attach($ingredient->id, [
                        'quantity' => rand(1, 250),
                        'presentation' => $presentation[rand(0,2)]
                    ]);
                }
            });
        }
    }
}
