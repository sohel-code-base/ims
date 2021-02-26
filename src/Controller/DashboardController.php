<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(Request $request,ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Product added successfully into Database!');

            return $this->redirectToRoute('app_index');

        }
        return $this->render('pages/index.html.twig',[
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit")
     * @param $id
     * @param Request $request
     * @param ProductRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editProduct($id, Request $request, ProductRepository $repository)
    {
        $product = $repository->find($id);

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Product updated successfully!');

            return $this->redirectToRoute('app_index');

        }
        return $this->render('product/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param ProductRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="product_delete")
     */
    public  function deleteProduct($id, ProductRepository $repository)
    {
        $product = $repository->find($id);
        if ($product){
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            $this->addFlash('success', 'Product deleted successfully!');

            return $this->redirectToRoute('app_index');
        }else{
            $this->addFlash('error', 'Product not found!');
            return $this->redirectToRoute('app_index');
        }
    }


}
