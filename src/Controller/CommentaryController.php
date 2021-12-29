<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommentaryController extends AbstractController
{
    /**
     * @Route("/ajouter-un-commentaire?article_id={id}", name="add_commentary" methods={"GET"})
     */
    // public function addCommentary(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     // $commentary = '';
    //     // return $this->render('.html.twig', [
    //     //     'commentary' => $commentary
    //     // ]);
    // }
}
