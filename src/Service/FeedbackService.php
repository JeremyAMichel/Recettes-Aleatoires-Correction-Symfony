<?php

namespace App\Service;

use App\Entity\Feedback;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class FeedbackService
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function createFeedback(array $data)
    {
        // reference to the Recipe entity to avoid loading the whole object
        $recipeReference = $this->entityManager->getReference(Recipe::class, $data['recipe']);

        if (!$recipeReference) {
            throw new \Exception("Recipe not found");
        }

        $user = $this->security->getUser();

        if (!$user) {
            throw new \Exception("User not found");
        }

        $feedback = new Feedback();
        $feedback->setComment($data['comment']);
        $feedback->setNote($data['note']);
        $feedback->setRecipe($recipeReference);
        $feedback->setUser($user);

        $this->entityManager->persist($feedback);
        $this->entityManager->flush();
    }

    
}
