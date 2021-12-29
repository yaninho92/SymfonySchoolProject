<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de votre article',
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'Sous-titre de votre article' 
            ])
            ->add('content', TextareaType::class, [
                'label' => False,
                'attr' => [
                    'placeholder' => 'Contenu de votre article'
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class, 
                'choice_label' => 'name',
                'label' => 'Choissisez la catégories'    
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'mimeTypes' =>['image/jpeg','image/png'],
                        'mimeTypesMessage' => 'Les types de fichiers autorisées sont : .jpeg et .png',
                    ])
                ],
                'attr' => [
                    'data-default_file' => $options['photo']
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'allow_file_upload' => true,
            'photo' => null,
        ]);
    }
}
