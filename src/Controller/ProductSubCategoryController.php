<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductSubCategoryController extends AbstractController
{
    /**
     * @Route("/product/sub/category", name="product_sub_category")
     */
    public function index(): Response
    {
        return $this->render('product_sub_category/index.html.twig', [
            'controller_name' => 'ProductSubCategoryController',
        ]);
    }
}
