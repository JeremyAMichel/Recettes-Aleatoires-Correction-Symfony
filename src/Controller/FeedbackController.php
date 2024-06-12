<?php

namespace App\Controller;

use App\Service\FeedbackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback')]
    public function index(FeedbackService $feedbackService): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $feedbacks = $this->getUser()->getFeedbacks();

        return $this->render('feedback/index.html.twig', [
            'controller_name' => 'FeedbackController',
            'feedbacks' => $feedbacks,
        ]);
    }
}
