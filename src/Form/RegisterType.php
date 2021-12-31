<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType
{

    public function __construct(Security $security)
    {
        $this->security = $security;   
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'required' => 'true',
                'attr' => [
                    'placeholder' => 'Entrez votre e-mail'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champs ne peut être vide, veuillez entrez un email'
                    ]) 
                ]
            ]);
        if (! $this->security->getUser()) {
            $builder
                ->add('password', PasswordType::class, [
                    'label' => 'mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez un mot de passe'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Ce champs ne peut être vide, veuillez entrez un mot de passe'
                        ]) 
                    ]
                ]);
        }
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champs ne peut être vide, veuillez entrez votre prénom']),
                    new Length([
                        'min' => 2,
                        'max' => 20,
                        'minMessage' => 'Votre prénom est trop court, il doit contenir au minimum {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez votre Nom'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Ce champs ne peut être vide, veuillez entrez votre prénom']),
                    new Length([
                        'min' => 2,
                        'max' => 20,
                        'minMessage' => 'Votre prénom est trop court, il doit contenir au minimum {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'd-block col-3 mx-auto btn btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
