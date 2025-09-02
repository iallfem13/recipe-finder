<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testResultsByFilters()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        // match by author
        $response = $this->getJson('/api/recipe/search?filters={"author":"iv@example.com"}');
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Salmon bowl']);
        
        // match by ingredient
        $response = $this->getJson('/api/recipe/search?filters={"ingredient":"salmon"}');
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Salmon bowl']);

        // match by keyword
        $response = $this->getJson('/api/recipe/search?filters={"keyword":"bowl"}');
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Salmon bowl']);

        $filters = [
            'author' => 'iv@example.com',
            'keyword' => 'salmon',
            'ingredient' => 'salmon'
        ];

        // match by all filters
        $response = $this->getJson('/api/recipe/search?filters=' . json_encode($filters));
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Salmon bowl']);
    }

    public function testEmptyFiltersQuery()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        $expected = [
            'status' => 'error',
            'message' => 'Invalid request body',
            'data' => [
                'filters' => ['The filters field is required.']
            ]
        ];

        // match by author
        $response = $this->getJson('/api/recipe/search?filters={}');
        $response->assertStatus(200)
                 ->assertJson($expected);
    }

    public function testSearchReturnsPagination()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        Recipe::factory()->count(12)->create([
            'user_id' => $user->id
        ]);

        // assert different pages
        $responsePage1 = $this->getJson('/api/recipe/search?filters={"author":"iv@example.com"}');
        $responsePage1->assertStatus(200)
                      ->assertJsonPath('data.current_page', 1)
                      ->assertJsonPath('data.per_page', 10);

        $responsePage2 = $this->getJson('/api/recipe/search?filters={"author":"iv@example.com"}&page=2');
        $responsePage2->assertStatus(200)
                      ->assertJsonPath('data.current_page', 2);

        // assert results are different per page
        $this->assertNotSame($responsePage1, $responsePage2);
    }

    public function testNoResultsByFilters()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        $failFilters = [
            'author' => 'iv@example.com',
            'keyword' => 'garlic', // should not be found because of this
            'ingredient' => 'salmon'
        ];

        $expected = [
            'status' => 'success', 
            'data' => []
        ];

        // fail if all filters are not matching
        $response = $this->getJson('/api/recipe/search?filters=' . json_encode($failFilters));
        $response->assertStatus(200)
                 ->assertJsonFragment($expected);
    }

    public function testReturnsRecipeBySlug()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        $expected = [
            'status' => 'success',
            'data' => $recipe->toArray()
        ];

        $response = $this->getJson('api/recipe/' . $recipe->slug);
        $response->assertStatus(200)
                 ->assertJson($expected);
    }

    public function testNoRecipeFoundBySlug()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        $expected = [
            'status' => 'error', 
            'data' => []
        ];

        $response = $this->getJson('api/recipe/' . $recipe->slug . '-345'); // wrong slug
        $response->assertStatus(401)
                 ->assertJsonFragment($expected);
    }

    public function testInvalidSlug()
    {
        list($user, $ingredient, $recipe) = $this->generateFactories();

        // fail improper slug
        $expected = [
            'status' => 'error',
            'message' => 'Invalid recipe'
        ];

        $response = $this->getJson('api/recipe/' . ($recipe->slug . '<#345>')); // wrong slug
        $response->assertStatus(401)
                 ->assertJsonFragment($expected);
    }

    private function generateFactories()
    {
        $user = User::factory()->create(['email' => 'iv@example.com']);
        $ingredient = Ingredient::factory()->create(['name' => 'salmon']);
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'title' => 'Salmon bowl',
            'slug' => 'salmon-bowl',
            'description' => 'Made with delicious WAC salmon',
            'instructions' => json_encode(['Fry your salmon', 'Eat it']),
        ]);

        // add random presentation
        $presentation = ['choped', 'minced', 'crushed'];

        $recipe->ingredients()->attach($ingredient->id, [
                    'quantity' => rand(1, 250),
                    'presentation' => $presentation[rand(0,2)]
                ]);

        return [$user, $ingredient, $recipe];
    }
}
