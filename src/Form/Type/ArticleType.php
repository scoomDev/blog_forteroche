<?php

namespace forteroche\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use forteroche\Domain\Chapter;

class ArticleType extends AbstractType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('image', FileType::class, ['label' => 'Choisissez une image d\'en-tête'])*/
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('chapter', TextType::class, [ 'label' => 'Chapitre' ])
            ->add('content', TextareaType::class, [ 'required' => false ]);
    }

    public function getName()
    {
        return 'article';
    }
}