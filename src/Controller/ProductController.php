<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    /**
     * @Route("/product/info", name="product_info")
     * @param ProductRepository $repository
     * @return Response
     */
    public function infoProduct(ProductRepository $repository): Response
    {
        $productInfo = $repository->findAll();
        return $this->render('product/index.html.twig',[
            'productInfo' => $productInfo,
        ]);
    }

    /**
     * @Route("/product/new", methods={"GET","POST"}, name="new_product")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $product->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Product added!');
            return $this->redirectToRoute('new_product');
        }

        return $this->render('product/addProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
