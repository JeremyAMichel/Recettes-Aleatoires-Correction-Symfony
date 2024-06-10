<?php

namespace App\Command;

use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:parse-recipes',
    description: 'Parse the JSON file recipes.json (in "data" folder) and insert the recipes in the database.',
)]
class ParseRecipesCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $json = file_get_contents(__DIR__ . '/../../data/recipes.json');
        $data = json_decode($json, true);

        if (isset($data['recipes'])) {

            foreach ($data['recipes'] as $item) {
                // Vérifie si une recette avec le même nom existe déjà
                $existingRecipe = $this->entityManager->getRepository(Recipe::class)->findOneBy(['name' => $item['name']]);
                if (!$existingRecipe) {
                    $recipe = new Recipe();
                    $recipe->setName($item['name']);
                    $recipe->setIngredients($item['ingredients']);
                    $recipe->setPreparationTime($item['preparationTime']);
                    $recipe->setCookingTime($item['cookingTime']);
                    $recipe->setServes($item['serves']);

                    $this->entityManager->persist($recipe);
                } else {
                    $io->note(sprintf('La recette "%s" existe déjà, elle n\'a pas été insérée à nouveau.', $item['name']));
                }
            }

            $this->entityManager->flush();


            $io->success('Recipes have been successfully imported.');

            return Command::SUCCESS;
        } else {
            $io->error('The "recipes" array does not exist in the provided JSON file.');

            return Command::FAILURE;
        }
    }
}
