<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register", methods={"GET|POST"})
     * @param Request $request
     * @return Response
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            // $user->setCreatedAt(new DateTime());

            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            // $user->setRoles(['ROLE_USER']);
            
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/supprimer/user/{id}", name="soft_delete_user", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function softDeleteUser(User $user, EntityManagerInterface $entityManager): Response
    {   
   
        $user->setDeletedAt(new DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur ".$user->getFirstname()." à bien été archivé !!");

        return $this->redirectToRoute('show_dashboard');
    }
}