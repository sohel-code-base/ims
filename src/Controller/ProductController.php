<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductPurchase;
use App\Form\ProductPurchaseType;
use App\Form\ProductType;
use App\Repository\ProductPurchaseRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    /**
     * @Route("/", name="all_product")
     * @param ProductPurchaseRepository $repository
     * @return Response
     */
    public function index(ProductPurchaseRepository $repository): Response
    {
        $allProducts = $repository->findAll();
        return $this->render('product/index.html.twig',[
            'allProducts' => $allProducts,
        ]);
    }

    /**
     * @Route("/product/info", name="product_info")
     * @param ProductRepository $repository
     * @return Response
     */
    public function infoProduct(ProductRepository $repository): Response
    {
        $productInfo = $repository->findAll();
        return $this->render('product/infoProduct.html.twig',[
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
            return $this->redirectToRoute('all_product');
        }

        return $this->render('product/addProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/purchase", name="product_purchase")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function productPurchase(Request $request)
    {
        $productPurchase = new ProductPurchase();
        $form = $this->createForm(ProductPurchaseType::class, $productPurchase);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $purchaseDate = new \DateTime($form->get('purchaseDate')->getData());

            $em = $this->getDoctrine()->getManager();
            $productPurchase->setPurchaseDate($purchaseDate);
            $productPurchase->setCreatedAt(new \DateTime('now'));
            $em->persist($productPurchase);
            $em->flush();
            $this->addFlash('success', 'Product added into Database!');
            return $this->redirectToRoute('all_product');
        }
        return $this->render('product/purchaseProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
