<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_home", methods={"GET"})
     */
    public function home(EntityManagerInterface $entityManager)
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('default/home.html.twig',[
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/render-categories", name="render_categories", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function renderCategories(EntityManagerInterface $entityManager): Response
    {   
        $categories = $entityManager->getRepository(Category::class)->findAll();
        
        return $this->render('rendered/nav_categories.html.twig', [
            'categories' => $categories
        ]);
    }

}