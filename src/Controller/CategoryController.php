<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(Request $request, CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'New category added!');

            return $this->redirectToRoute('category');
        }
        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit")
     */
    public function editCategory($id, Request $request, CategoryRepository $repository)
    {
        $category = $repository->find($id);
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Category has been updated!');

            return $this->redirectToRoute('category');
        }
        return $this->render('category/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="category_delete")
     */
    public function deleteCategory($id, CategoryRepository $repository)
    {
        $category = $repository->find($id);
        if ($category){
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Category deleted successfully!');

            return $this->redirectToRoute('category');
        }else{
            $this->addFlash('error', 'Category not found!');
            return $this->redirectToRoute('category');
        }
    }
}
