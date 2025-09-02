<?php

namespace App\Models;

use App\Models\User;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'slug', 'servings', 'cook_time', 'user_id', 'image_url', 'description', 'instructions'];
    protected $casts = ['instructions' => 'array'];

    /**
     * Generate slug on create/update
     *
     * @return void
     */
    protected static function booted() {
        static::creating(function ($recipe) {
            $recipe->slug = \Str::slug($recipe->title);
        });

        static::updating(function ($recipe) {
            $recipe->slug = \Str::slug($recipe->title);
        });
    }

    /**
     * Recipe belongs to a user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Recipe-ingredients relationship
     *
     * @return BelongsToMany
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient', 'recipe_id', 'ingredient_id')
                    ->using(RecipeIngredient::class)
                    ->withPivot(['quantity', 'presentation']);
    }

    /**
     * Use slug for route model binding
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
