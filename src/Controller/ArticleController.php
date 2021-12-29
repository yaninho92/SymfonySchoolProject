<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/creer-un-article", name="create_article", methods={"GET|POST"})
     * @param Request $request
     * @param SluggerInterface $slugger 
     * @param Response
     */
    public function createArticle(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $article = new Article;
        
        $form = $this->createForm(ArticleType::class, $article)
            ->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $article->setAuthor($this->getUser());
            $article = $form->getData();

            $file = $form->get('photo')->getData();

            $article->setAlias($slugger->slug($article->getTitle()));

            if($file){

                // $allowMimeTypes = ['image/jpeg','image/png'];

                // if(in_array($file->getMimeType(), $allowMimeTypes)){
                // $originalFilename = pathinfo( $file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = '.' . $file->guessExtension();

                $safeFilename = $article->getAlias();
                // $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '_' . uniqid() . $extension;
                
                try {
                    
                    $file->move($this->getParameter('uploads_dir'),$newFilename);
                    $article->setPhoto($newFilename);

                } catch ( FileException $exeception ) {}
                
                // }else{

                // }
                
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez créé un nouvelle article !!');

            return $this->redirectToRoute('default_home');
        }
        return $this->render('article/form_article.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/editer-un-article_{id}", name="edit_article", methods={"GET|POST"})
     */
    public function editArticle(Article $article, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {   
        
        $form = $this->createForm(ArticleType::class, $article, [
            'photo' => $article->getPhoto() 
        ]);

        $originalPhoto = $article->getPhoto();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
        
            $article->setUpdatedAt(new DateTime());
            $article->setAlias($slugger->slug($article->getTitle()));
            
            $file = $form->get('photo')->getData();

            if($file){

                $extension = '.' . $file->guessExtension();

                $safeFilename = $article->getAlias();

                $newFilename = $safeFilename . '_' . uniqid() . $extension;
                
                try {
                    
                    $file->move($this->getParameter('uploads_dir'),$newFilename);
                    $article->setPhoto($newFilename);

                } catch ( FileException $exeception ) {}
                
            } else {
                $article->setPhoto($originalPhoto);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "L'article ".$article->getTitle()."à bien été modifé !!");

            return $this->redirectToRoute('show_dashboard');
            
        }
        
        return $this->render('article/form_article.html.twig', [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * @Route("/voir-un-article_{id}", name="show_article", methods={"GET"})
     */
    public function showArticle(Article $article): Response
    {
        return $this->render('article/show_article.html.twig', [
            'article' => $article
        ]);
    }

}