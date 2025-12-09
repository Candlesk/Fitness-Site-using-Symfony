<?php

namespace App\Form;

use App\Entity\Exercise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExerciseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter exercise name']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Describe the exercise...']
            ])
            ->add('muscleGroup', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Chest, Legs, Back']
            ])
            ->add('equipment', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Dumbbells, Bodyweight, Barbell']
            ])
            ->add('difficulty', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'e.g., Beginner, Intermediate, Advanced']
            ])
            ->add('image', FileType::class, [
                'label' => 'Exercise Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercise::class,
        ]);
    }
}