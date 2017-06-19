<?php

namespace forteroche\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\File;
use forteroche\Domain\Article;

class ArticleType extends AbstractType
{ 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $article = new Article();
        $builder
            ->add('image', FileType::class, [
                'label' => 'Choisissez une image d\'en-tête', 
                'data_class' => $article->getImage(),
                'required' => false
            ])
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('chapter', TextType::class, [ 'label' => 'Chapitre' ])
            ->add('content', TextareaType::class, [ 'required' => false ]);
    }

    public function getName()
    {
        return 'article';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Article::class,
        ));
    }
}
