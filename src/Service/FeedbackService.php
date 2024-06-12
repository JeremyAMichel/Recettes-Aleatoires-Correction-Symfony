<?php

namespace App\Service;

use App\Entity\Feedback;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;

class FeedbackService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createFeedback(array $data)
    {
        $recipe = $this->entityManager->getRepository(Recipe::class)->find($data['recipe']);

        if (!$recipe) {
            throw new \Exception("Recipe not found");
        }
        
        $feedback = new Feedback();
        $feedback->setComment($data['comment']);
        $feedback->setNote($data['note']);
        $feedback->setRecipe($recipe);

        $this->entityManager->persist($feedback);
        $this->entityManager->flush();
    }
}
