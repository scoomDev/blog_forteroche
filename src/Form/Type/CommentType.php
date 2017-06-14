<?php
namespace forteroche\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\hiddenType;

class CommentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('parent_id', hiddenType::class)
            ->add('depth', hiddenType::class)
            ->add('reporting', hiddenType::class)
            ->add('content', TextareaType::class, ['label' => 'Message'])
            ->add('author', TextType::class, ['label' => 'Pseudo']);
    }

    public function getName() {
        return 'comment';
    }

}