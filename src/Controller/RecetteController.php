<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Service\FeedbackService;
use App\Service\RecipeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette')]
    public function index(RecipeService $recipeService, Request $request, FeedbackService $feedbackService): Response
    {
        try {
            $recipe = $recipeService->getRandomRecipeAsArray();
        } catch (\Exception $e) {
            // Erreur pour le flash bag
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            return $this->render('recette/error.html.twig');
        }

        $form = $this->createForm(FeedbackType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedbackData = $form->getData();
            $feedbackService->createFeedback($feedbackData + ['recipe' => $recipe['id']]);

            $this->addFlash('success', 'Votre retour a bien été enregistré !');

            // Redirection pour éviter la soumission multiple si l'utilisateur rafraîchit la page
            return $this->redirectToRoute('app_recette');
        }

        return $this->render('recette/index.html.twig', [
            'controller_name' => 'RecetteController',
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }
}
