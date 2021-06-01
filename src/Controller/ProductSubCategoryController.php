<?php

namespace App\Controller;

use App\Entity\SubCategory;
use App\Form\ProductSubCategoryType;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductSubCategoryController
 * @package App\Controller
 * @Route("/product/sub-category")
 */
class ProductSubCategoryController extends AbstractController
{
    /**
     * @Route("/", name="all_sub_category")
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
     * @Route("/new", name="product_new_sub_category")
     * @param Request $request
     * @return Response
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
            return $this->redirectToRoute('product_new_sub_category');
        }
        return $this->render('product_sub_category/addSubCategory.html.twig',[
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="edit_sub_category")
     * @param Request $request
     * @param $id
     * @param SubCategoryRepository $repository
     * @return Response
     */
    public function editSubCategory(Request $request, $id, SubCategoryRepository $repository): Response
    {
        $findSubCat = $repository->find($id);
        $form = $this->createForm(ProductSubCategoryType::class, $findSubCat)->remove('createdAt')->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $findSubCat->setUpdatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($findSubCat);
            $em->flush();

            $this->addFlash('update', 'Sub-category updated!');
            return $this->redirectToRoute('all_sub_category');
        }
        return $this->render('product_sub_category/editSubCategory.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param SubCategoryRepository $subCategoryRepository
     * @param ProductRepository $productRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="delete_sub_category")
     */
    public function deleteSubCategory($id, SubCategoryRepository $subCategoryRepository, ProductRepository $productRepository)
    {
        $findSubCat = $subCategoryRepository->find($id);
        if ($findSubCat){
            $findProduct = $productRepository->findBy(['subCategory' => $findSubCat]);
            if (!$findProduct){
                $em = $this->getDoctrine()->getManager();
                $em->remove($findProduct);
                $em->flush();
                $this->addFlash('success', 'Sub-category has been deleted!');
                return $this->redirectToRoute('all_sub_category');

            } else {
                $this->addFlash('error', 'Sub-category that has products can not be deleted!');
                return $this->redirectToRoute('all_sub_category');
            }
        } else {
            $this->addFlash('error', 'Sub-category not found!');
            return $this->redirectToRoute('all_sub_category');
        }
    }
}
