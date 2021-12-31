<?php

namespace App\Form;

use App\Entity\Commentary;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => 'Ecrivez votre commentaire ici'
                ],
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Commenter <i class="far fa-comments"></i>',
                'attr' => [
                    'class' => 'd-block col-3 mx-auto btn btn-success'
                ],
                'label_html' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentary::class,
        ]);
    }
}
