<?php

namespace App\Controller;

use App\Service\RecipeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette')]
    public function index(RecipeService $recipeService): Response
    {
        try {
            $recipe = $recipeService->getRandomRecipeAsArray();
        } catch (\Exception $e) {
            // Add error message to flash bag
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
    
            return $this->render('recette/error.html.twig');
        }
    
        return $this->render('recette/index.html.twig', [
            'controller_name' => 'RecetteController',
            'recipe' => $recipe,
        ]);
    }
}
