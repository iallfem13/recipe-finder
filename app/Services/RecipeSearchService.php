<?php

namespace App\Services;

use App\Models\Recipe;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Handles search request for recipes based on filters: author, ingredient, keyword
 *  If more than one filter passed, it will act as "AND"
 *  Keyword will be searched in ingredient list as in instructions
 * 
 * If no recognized filter passed, then nothing will be returned
 */
class RecipeSearchService
{
    /**
     * Searches for recipes that matches a criteria
     *
     * @param array $filters - Options: author, ingredient, keyword
     * 
     * @return LengthAwarePagination
     */
    public function search(array $filters = [], int $limit = 10, int $page = 1): LengthAwarePaginator
    {
        // eager load our filters
        $query = Recipe::query()->with(['user', 'ingredients']);

        foreach ($filters as $criteria => $value) {
            switch ($criteria) {
                case 'author':
                    $query->whereHas('user', function ($q) use ($value) {
                        $q->where('email', $value);
                    });
                    
                    break;
                case 'ingredient':
                    $query->whereHas('ingredients', function ($q) use ($value) {
                        $q->where('name', 'like', '%' . $value . '%');
                    });

                    break;
                case 'keyword':
                    $search = '%' . $value . '%';

                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', $search)
                          ->orWhere('description', 'like', $search)
                          ->orWhere('instructions', 'like', $search)
                          ->orWhereHas('ingredients', function ($subQ) use ($search) {
                                $subQ->where('name', 'like', $search);
                          });
                    });

                    break;
                default:
                    break;
            }
        }
        
        return $query->paginate(perPage: $limit, page: $page);
    }

    /**
     * Returns a recipe (if exists)
     *
     * @param string $slug - recipe's slug
     * 
     * @return Recipe
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findBySlug(string $slug): Recipe
    {
        return Recipe::with(['user', 'ingredients'])
                     ->where('slug', $slug)
                     ->firstOrFail();

                    
    }
}