<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Entity\Commentary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/creer-un-article", name="create_article", methods={"GET|POST"})
     * @param Request $request
     * @param SluggerInterface $slugger 
     * @param EntityManagerInterface $entityManager
     * @return Response
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
     * @param Request $request
     * @param SluggerInterface $slugger 
     * @param EntityManagerInterface $entityManager
     * @return Response
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
     * @Route("/admin/supprimer/article/{id}", name="soft_delete_article", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function softDeleteArticle(Article $article, Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {   
   
        $article->setDeletedAt(new DateTime());

        $entityManager->persist($article);
        $entityManager->flush();

        $this->addFlash('success', "L'article ".$article->getTitle()."à bien été archivé !!");

        return $this->redirectToRoute('show_dashboard');
    }

    /**
     * @Route("/voir/{cat_alias}/{art_alias}", name="show_article", methods={"GET"})
     * @ParamConverter("article", options={"mapping": {"art_alias" : "alias"}})
     * @param SluggerInterface $slugger 
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function showArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        $commentaries = $entityManager->getRepository(Commentary::class)->findBy(['article' => $article->getId()]);

        return $this->render('article/show_article.html.twig', [
            'article' => $article,
            'commentaries' => $commentaries
        ]);
    }

    /**
     * @Route("voir/categories/{alias}", name="show_articles_from_category", methods={"GET"})
     * @ParamConverter("article", options={"mapping": {"art_alias" : "alias"}})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function showArticlesFromCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findBy(['category' => $category->getId()]);

        return $this->render('article/show_articles_from_category.html.twig', [
            'articles' => $articles,
            'category' => $category
        ]);
    }

}