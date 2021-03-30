<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCategoryController extends AbstractController
{
    /**
     * @Route("/product/category", name="product_category")
     */
    public function index(): Response
    {
        return $this->render('product_category/index.html.twig', [
            'controller_name' => 'ProductCategoryController',
        ]);
    }
}
