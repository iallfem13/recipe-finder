<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RecipeRequest;
use App\Services\RecipeSearchService;

class RecipeController extends Controller
{
    protected RecipeSearchService $searchService;

    public function __construct(RecipeSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Search for recipes given a certain criteria. 
     *  Returns a Json response with pagination params and recipes (if found)
     *
     * @param RecipeRequest $request
     * @return JsonResponse
     */
    public function search(RecipeRequest $request): JsonResponse
    {
        $filters = [];

        // generate filters based on the query params - filters or q
        if ($request->filled('filters')) {
            $filters = json_decode($request->filters, true);
        } else if ($request->filled('q')) {
            $filters = [
                ['keyword' => $request->q]
            ];
        }

        $limit = $request->query('limit', 10); // number of results per page
        $page = $request->query('page', 1);

        try {
            $recipes = $this->searchService->search($filters, $limit, $page);

            return ApiResponse::success($recipes);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    /**
     * Return a recipe by its slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function view(string $slug): JsonResponse
    {
        try {
            // sanitize input
            if (!preg_match("/^[a-z0-9-]+$/", $slug)) {
                throw new \Exception('Invalid recipe');
            }

            $recipe = $this->searchService->findBySlug($slug);

            return ApiResponse::success($recipe);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
