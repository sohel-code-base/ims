<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\ProductCategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCategoryController extends AbstractController
{
    /**
     * @Route("/product/category", name="all_category")
     * @param CategoryRepository $repository
     * @return Response
     */
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->render('product_category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    /**
     * @Route("/product/category/new",methods={"GET","POST"}, name="product_new_category")
     */
    public function addCategory(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(ProductCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $category->setCreatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Category added!');
            return $this->redirectToRoute('all_category');
        }
        return $this->render('product_category/addCategory.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
