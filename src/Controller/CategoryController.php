<?php

namespace App\Controller;

use DateTime;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/archiver/category/{id}", name="soft_delete_category", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function softDeleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {   
   
        $category->setDeletedAt(new DateTime());

        $entityManager->persist($category);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie ".$category->getName()." bien été archivé !!");

        return $this->redirectToRoute('show_dashboard');
    }

    /**
     * @Route("/admin/supprimer/category/{id}", name="hard_delete_category", methods={"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function hardDeleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {   

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', "La catégorie ".$category->getName()." bien été supprimmer !!");

        return $this->redirectToRoute('show_dashboard');
    }
}
