<?php

namespace forteroche\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', TextType::class, ['label' => 'Numéro'])
            ->add('title', TextType::class, ['label' => 'Titre']);
    }

    public function getName()
    {
        return 'chapter';
    }
}