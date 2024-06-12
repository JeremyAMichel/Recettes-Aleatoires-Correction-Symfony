<?php

namespace App\Controller;

use App\Form\FeedbackType;
use App\Service\FeedbackService;
use App\Service\RecipeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette')]
    public function index(RecipeService $recipeService, Request $request, FeedbackService $feedbackService, SessionInterface $session): Response
    {
        $recipeId = null;

        // Si la page est rafraîchie, on ne garde pas l'ID de la recette en session
        if ($request->isMethod('GET') && !$session->getFlashBag()->has('success_feedback')) {
            $session->remove('recetteId');
        }

        if ($session->get('recetteId') !== null) {
            $recipeId = $session->get('recetteId');
        }


        try {
            if ($recipeId) {
                // Charge la recette spécifique sur laquelle l'utilisateur a laissé un feedback
                $recipe = $recipeService->getRecipeByIdAsArray($recipeId);
            } else {
                // Aucun feedback ou ID de recette, charge une recette au hasard
                $recipe = $recipeService->getRandomRecipeAsArray();
            }
        } catch (\Exception $e) {
            // Erreur pour le flash bag
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            return $this->render('recette/error.html.twig');
        }

        $form = $this->createForm(FeedbackType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                // Ajoute un message flash
                $this->addFlash('error_feedback', 'Vous devez être connecté pour donner un feedback.');

                // Redirige vers la page de connexion
                return $this->redirectToRoute('app_login');
            }

            $feedbackData = $form->getData();
            $feedbackService->createFeedback($feedbackData + ['recipe' => $recipe['id']]);

            $this->addFlash('success_feedback', 'Votre retour a bien été enregistré !');

            // $session->set('recetteId', $recipe['id']);

            // Redirection pour éviter la soumission multiple si l'utilisateur rafraîchit la page
            return $this->redirectToRoute('app_recette');
        }

        $session->set('recetteId', $recipe['id']);

        return $this->render('recette/index.html.twig', [
            'controller_name' => 'RecetteController',
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }
}
