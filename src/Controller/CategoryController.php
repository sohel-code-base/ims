<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/category", name="category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="_index")
     * @param Request $request
     * @param CategoryRepository $repository
     * @return Response
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

            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_edit")
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

            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="_delete")
     * @param $id
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategory($id, CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $category = $categoryRepository->find($id);
        $findProducts = $productRepository->findBy(['category' => $category]);
        if ($category){
            $em = $this->getDoctrine()->getManager();
            foreach ($findProducts as $product){
                $product->setCategory(null);
                $em->persist($product);
                $em->flush();
            }
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Category deleted successfully!');

            return $this->redirectToRoute('category_index');
        }else{
            $this->addFlash('error', 'Category not found!');
            return $this->redirectToRoute('category_index');
        }
    }
}
