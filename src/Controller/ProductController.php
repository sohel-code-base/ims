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
        $allProducts = $repository->getTotalProduct();
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
            return $this->redirectToRoute('product_info');
        }

        return $this->render('product/addProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/purchase", name="product_purchase")
     * @param Request $request
     * @param ProductPurchaseRepository $purchaseRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function productPurchase(Request $request, ProductPurchaseRepository $purchaseRepository)
    {
        $productPurchase = new ProductPurchase();
        $form = $this->createForm(ProductPurchaseType::class, $productPurchase);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $purchaseDate = new \DateTime($form->get('purchaseDate')->getData());
            $product = $form->get('product')->getData();
            $findExistingProduct = $purchaseRepository->findOneBy(['product' => $product]);

            if ($findExistingProduct){
                $preQuantity = $findExistingProduct->getQuantity();

                $purchaseQuantity = $form->get('quantity')->getData();
                $purchasePrice = $form->get('purchasePrice')->getData();
                $salePrice = $form->get('salePrice')->getData();
                $power = $form->get('proPower')->getData();


                $findExistingProduct->setPurchasePrice($purchasePrice);
                $findExistingProduct->setSalePrice($salePrice);
                $findExistingProduct->setQuantity($preQuantity + $purchaseQuantity);
                $findExistingProduct->setPurchaseDate($purchaseDate);
                $findExistingProduct->setProPower($power ? $power: null);
                $findExistingProduct->setCreatedAt(new \DateTime('now'));
                $em->persist($findExistingProduct);
                $em->flush();
                $this->addFlash('update', 'Quantity Increased!');
                return $this->redirectToRoute('all_product');
            }
            $productPurchase->setPurchaseDate($purchaseDate);
            $productPurchase->setCreatedAt(new \DateTime('now'));
            $em->persist($productPurchase);
            $em->flush();
            $this->addFlash('success', 'New Product added into Database!');
            return $this->redirectToRoute('all_product');
        }
        return $this->render('product/purchaseProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
