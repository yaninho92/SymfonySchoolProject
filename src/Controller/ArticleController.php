<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/creer-un-article.html", name="create_article", methods={"GET|POST"})
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

}