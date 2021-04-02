<?php

namespace App\Controller;

use App\Entity\SubCategory;
use App\Form\ProductSubCategoryType;
use App\Repository\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductSubCategoryController extends AbstractController
{
    /**
     * @Route("/product/sub/category", name="all_sub_category")
     * @param SubCategoryRepository $repository
     * @return Response
     */
    public function index(SubCategoryRepository $repository): Response
    {
        $subCategories = $repository->findAll();
        return $this->render('product_sub_category/index.html.twig', [
            'subCategories' => $subCategories,
        ]);
    }
    /**
     * @Route("/product/sub/category/new", name="product_new_sub_category")
     */
    public function addSubCategory(Request $request): Response
    {
        $subCategory = new SubCategory();
        $form = $this->createForm(ProductSubCategoryType::class, $subCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $subCategory->setCreatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($subCategory);
            $em->flush();

            $this->addFlash('success', 'Sub-Category added!');
            return $this->redirectToRoute('all_sub_category');
        }
        return $this->render('product_sub_category/addSubCategory.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
