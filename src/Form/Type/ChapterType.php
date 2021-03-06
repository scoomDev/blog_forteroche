<?php

namespace forteroche\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', textType::class, ['label' => 'Titre du chapitre'])
            ->add('number', TextType::class, ['label' => 'Numéro du chapitre']);
    }

    public function getName()
    {
        return 'chapter';
    }
}
