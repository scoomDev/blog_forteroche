<?php

namespace forteroche\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use forteroche\Domain\header;

class HeaderType extends AbstractType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $header = new Header();
        $builder
            ->add('image1', FileType::class, [
                'label' => 'Choisissez une image', 
                'data_class' => null,
                'required' => false
            ])
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('subtitle', TextType::class, ['label' => 'Sous-titre'])
            ->add('image2', FileType::class, [
                'label' => 'Choisissez une image', 
                'data_class' => null,
                'required' => false
            ]);
    }

    public function getName()
    {
        return 'header';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Header::class,
        ));
    }
}
