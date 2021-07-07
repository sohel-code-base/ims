<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\ProductCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

/**
 * Class ProductCategoryController
 * @package App\Controller
 * @Route("product/category")
 */
class ProductCategoryController extends AbstractController
{
    /**
     * @Route("/", name="all_category")
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
     * @Route("/new", name="product_new_category")
     * @param Request $request
     * @return Response
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
            return $this->redirectToRoute('product_new_category');
        }
        return $this->render('product_category/addCategory.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_category")
     * @param Request $request
     * @param $id
     * @param CategoryRepository $repository
     * @return Response
     */
    public function editCategory(Request $request, $id, CategoryRepository $repository): Response
    {
        $findCategory = $repository->find($id);
        $form = $this->createForm(ProductCategoryType::class, $findCategory)->remove('createdAt');
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $findCategory->setUpdatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($findCategory);
            $em->flush();

            $this->addFlash('update', 'Category updated!');
            return $this->redirectToRoute('all_category');
        }
        return $this->render('product_category/editCategory.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param CategoryRepository $categoryRepository
     * @param SubCategoryRepository $subCategoryRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="delete_category")
     */
    public function deleteCategory($id, CategoryRepository $categoryRepository, SubCategoryRepository $subCategoryRepository)
    {
        $findCategory = $categoryRepository->find($id);
        if ($findCategory){
            $findSubCat = $subCategoryRepository->findBy(['category' => $findCategory]);
            if (!$findSubCat){
                $em = $this->getDoctrine()->getManager();
                $em->remove($findCategory);
                $em->flush();
                $this->addFlash('success', 'Category has been deleted!');
                return $this->redirectToRoute('all_category');

            } else {
                $this->addFlash('error', 'Category that has sub-categories can not be deleted! Delete sub-categories first.');
                return $this->redirectToRoute('all_category');
            }
        } else {
            $this->addFlash('error', 'Category not found!');
            return $this->redirectToRoute('all_category');
        }
    }
}
