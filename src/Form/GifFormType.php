<?php

namespace App\Form;

use App\Entity\Gif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GifFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gif', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'URL'
                ],
                'label' => false
            ])
            ->add('author', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Author'
                ],
                'label' => false
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description'
                ],
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Gif::class,
        ]);
    }
}
