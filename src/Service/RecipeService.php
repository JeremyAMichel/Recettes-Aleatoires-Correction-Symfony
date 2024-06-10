<?php
namespace App\Service;

use App\Repository\RecipeRepository;

class RecipeService
{
    private $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function getRandomRecipeAsArray(): array
    {
        $randomRecipe = $this->recipeRepository->findRandomRecipe();

        if ($randomRecipe === null) {
            throw new \Exception('No recipe found');
        }

        return $randomRecipe->toArray();
    }


}