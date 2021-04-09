<?php

namespace App\Controller;

use App\Entity\ProductPurchase;
use App\Form\ProductPurchaseType;
use App\Repository\ProductPurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductPurchaseController extends AbstractController
{
    /**
     * @Route("/", name="all_purchase_product")
     * @param ProductPurchaseRepository $repository
     * @return Response
     */
    public function index(ProductPurchaseRepository $repository): Response
    {
        $allProducts = $repository->getTotalProduct();
        return $this->render('product_purchase/index.html.twig',[
            'allProducts' => $allProducts,
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
                $findExistingProduct->setUpdatedAt(new \DateTime('now'));
                $em->persist($findExistingProduct);
                $em->flush();
                $this->addFlash('update', 'Product Quantity Updated!');
                return $this->redirectToRoute('all_purchase_product');
            }
            $productPurchase->setPurchaseDate($purchaseDate);
            $productPurchase->setCreatedAt(new \DateTime('now'));
            $em->persist($productPurchase);
            $em->flush();
            $this->addFlash('success', 'New Product added into Database!');
            return $this->redirectToRoute('all_purchase_product');
        }
        return $this->render('product_purchase/purchaseProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/product/{id}/edit", name="edit_purchase_product")
     */
    public function editPurchaseProduct(Request $request, ProductPurchaseRepository $repository, $id)
    {
        $findProduct = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(ProductPurchaseType::class, $findProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $purchaseDateStringToDate = $form->get('purchaseDate')->getData();

            $findProduct->setUpdatedAt(new \DateTime('now'));
            $findProduct->setPurchaseDate(new \DateTime($purchaseDateStringToDate));
            $em->persist($findProduct);
            $em->flush();
            $this->addFlash('update', 'Record has been updated successfully!');
            return $this->redirectToRoute('all_purchase_product');
        }

        $purchaseDateToString = $form->get('purchaseDate')->getData()->format('Y-m-d');
        $form->get('purchaseDate')->setData($purchaseDateToString);

        return $this->render('product_purchase/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}/delete", name="delete_purchase_product")
     * @param ProductPurchaseRepository $purchase
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePurchaseProduct(ProductPurchaseRepository $purchase, $id)
    {
        $findProduct = $purchase->findOneBy(['id' => $id]);
        if($findProduct){
            $em = $this->getDoctrine()->getManager();
            $em->remove($findProduct);
            $em->flush();
            $this->addFlash('success','Product has been deleted!');
            return $this->redirectToRoute('all_purchase_product');
        }else{
            $this->addFlash('error','Record not found!');
            return $this->redirectToRoute('all_purchase_product');
        }
    }
}
