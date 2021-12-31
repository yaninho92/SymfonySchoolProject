<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\CommentaryType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CommentaryController extends AbstractController
{
    /**
     * @Route("/ajouter-un-commentaire?article_id={id}", name="add_commentary", methods={"GET|POST"})
     */
    public function addCommentary(Article $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentary = new Commentary;
        $form = $this->createForm(CommentaryType::class, $commentary)
            ->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
            $commentary = $form->getData();

            $commentary->setAuthor($this->getUser());
            $commentary->setCreatedAt(new DateTime());
            $commentary->setArticle($article);
            
            $entityManager->persist($commentary);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez commenté l'article !!!");
            return $this->redirectToRoute('show_article',[
                'id' => $article->getId()
            ]);
        }
        
        return $this->render('rendered/form_commentary.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/supprimer/{id}", name="soft_delete_commentary", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function softDeleteCommentary(Commentary $commentary, EntityManagerInterface $entityManager): Response
    {   
   
        $commentary->setDeletedAt(new DateTime());

        $entityManager->persist($commentary);
        $entityManager->flush();

        $this->addFlash('success', "L'article ".$commentary->getComment()."à bien été archivé !!");

        return $this->redirectToRoute('show_dashboard');
    }
}
