<?php

namespace App\Form;

use App\Entity\Feedback;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class) // Définir le champ comme une zone de texte
            ->add('note', IntegerType::class, [
                // Ajouter une contrainte pour que la note soit entre 0 et 5
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 5,
                        'notInRangeMessage' => 'La note doit être entre {{ min }} et {{ max }}',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
