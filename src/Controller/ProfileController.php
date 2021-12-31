<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/mon-profil", name="show_profile", methods={"GET"})
     */
    public function showProfile(): Response
    {
        return $this->render('profile/show_profile.html.twig');
    }
}
