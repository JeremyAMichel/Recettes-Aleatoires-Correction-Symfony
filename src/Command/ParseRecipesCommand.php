<?php

namespace App\Command;

use App\Entity\Recipe;
use App\Service\RecipeService;
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
    private EntityManagerInterface $entityManager;
    private RecipeService $recipeService;

    public function __construct(EntityManagerInterface $entityManager, RecipeService $recipeService)
    {
        $this->entityManager = $entityManager;
        $this->recipeService = $recipeService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $json = file_get_contents(__DIR__ . '/../../data/recipes.json');
        if ($json === false) {
            $io->error('Failed to read the JSON file.');
    
            return Command::FAILURE;
        }

        $data = json_decode($json, true);
        if ($data === null) {
            $io->error('Failed to parse the JSON file.');
    
            return Command::FAILURE;
        }

        if (isset($data['recipes'])) {

            foreach ($data['recipes'] as $item) {
                if (!isset($item['name'], $item['ingredients'], $item['preparationTime'], $item['cookingTime'], $item['serves'])) {
                    $io->warning('A recipe is missing some required data and has been skipped.');
    
                    continue;
                }

                // Vérifie si une recette avec le même nom existe déjà
                $existingRecipe = $this->entityManager->getRepository(Recipe::class)->findOneBy(['name' => $item['name']]);
                if (!$existingRecipe) {
                    $this->recipeService->createRecipe($item);
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
