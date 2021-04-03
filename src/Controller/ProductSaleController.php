<?php

namespace App\Controller;

use App\Entity\ProductSale;
use App\Form\ProductSaleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductSaleController extends AbstractController
{
    /**
     * @Route("/product/sale", name="all_product_sale")
     */
    public function index(): Response
    {
        return $this->render('product_sale/index.html.twig', [
            'controller_name' => 'ProductSaleController',
        ]);
    }

    /**
     * @Route("/product/sale/new", name="new_sale")
     * @param Request $request
     * @return Response
     */
    public function newSale(Request $request): Response
    {
        $productSale = new ProductSale();
        $form = $this->createForm(ProductSaleType::class, $productSale);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($productSale);
            $em->flush();
            $this->addFlash('success', 'Sale successful!');
            return $this->redirectToRoute('all_product_sale');
        }
        return $this->render('product_sale/newSale.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
