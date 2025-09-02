<?php

namespace App\Models;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecipeIngredient extends Pivot
{
    use SoftDeletes;

    protected $table = 'recipe_ingredient';

    protected $fillable = ['recipe_id', 'ingredient_id', 'quantity', 'presentation'];

    /**
     * Recipe in pivot relationship
     *
     * @return BelongsTo
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Ingredients in pivot relation
     *
     * @return BelongsTo
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Remove unnecessary zeros
     *
     * @param $value
     */
    public function getQuantityAttribute($value)
    {
        return sprintf('%g', $value);
    }
}
