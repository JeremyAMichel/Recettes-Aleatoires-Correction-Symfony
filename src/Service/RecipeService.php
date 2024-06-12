<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipeService
{
    private RecipeRepository $recipeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(RecipeRepository $recipeRepository, EntityManagerInterface $entityManager)
    {
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
    }

    public function getRandomRecipeAsArray(): array
    {
        $randomRecipe = $this->recipeRepository->findRandomRecipe();

        if ($randomRecipe === null) {
            throw new \Exception('No recipe found');
        }

        return $randomRecipe->toArray();
    }

    public function getRecipeByIdAsArray(int $id): array
    {
        $recipe = $this->recipeRepository->find($id);

        if ($recipe === null) {
            throw new \Exception('Recipe not found');
        }

        return $recipe->toArray();
    }

    public function createRecipe(array $data)
    {
        $recipe = new Recipe();
        $recipe->setName($data['name']);
        $recipe->setIngredients($data['ingredients']);
        $recipe->setPreparationTime($data['preparationTime']);
        $recipe->setCookingTime($data['cookingTime']);
        $recipe->setServes($data['serves']);

        $this->entityManager->persist($recipe);
    }
}
