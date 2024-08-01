<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new NotBlank(['message' => 'O título é obrigatório']),
                    new Length(['max' => 255, 'maxMessage' => 'O título não pode ter mais que 255 caracteres']),
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new Length(['max' => 255, 'maxMessage' => 'A descrição não pode ter mais que 255 caracteres']),
                ],
            ])
            ->add('body', null, [
                'constraints' => [
                    new NotBlank(['message' => 'O corpo do evento é obrigatório']),
                ],
            ])
            ->add('slug', null, [
                'constraints' => [
                    new NotBlank(['message' => 'O slug é obrigatório']),
                    new Length(['max' => 255, 'maxMessage' => 'O slug não pode ter mais que 255 caracteres']),
                ],
            ])
            ->add('start_date', null, [
                'constraints' => [
                    new NotBlank(['message' => 'A data de início é obrigatória']),
                ],
            ])
            ->add('end_date', null, [
                'constraints' => [
                    new NotBlank(['message' => 'A data de término é obrigatória']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
