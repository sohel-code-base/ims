<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductPurchaseRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class ProductController
 * @package App\Controller
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/list", name="product_info")
     * @param ProductRepository $repository
     * @return Response
     */
    public function itemInfo(ProductRepository $repository): Response
    {
        $productInfo = $repository->findAll();
        return $this->render('product/index.html.twig',[
            'productInfo' => $productInfo,
        ]);
    }

    /**
     * @Route("/new", methods={"POST"}, name="new_product")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newItem(Request $request)
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
    /**
     * @Route("/{id}/edit", methods={"GET","POST"}, name="edit_product")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editItem(Request $request, $id, ProductRepository $repository)
    {
        $findItem = $repository->find($id);

        $form = $this->createForm(ProductType::class, $findItem)->remove('createdAt');
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $findItem->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($findItem);
            $em->flush();
            $this->addFlash('update', 'Product details updated!');
            return $this->redirectToRoute('product_info');
        }

        return $this->render('product/editProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param ProductRepository $productRepository
     * @Route("/{id}/delete", name="delete_product")
     */
    public function deleteItem($id, ProductRepository $productRepository, ProductPurchaseRepository $purchaseRepository)
    {
        $findItem = $productRepository->find($id);
        if ($findItem){
            $findPurchase = $purchaseRepository->findBy(['product' => $findItem]);
            if (!$findPurchase){
                $em = $this->getDoctrine()->getManager();
                $em->remove($findItem);
                $em->flush();
                $this->addFlash('success', 'Item has been deleted!');
                return $this->redirectToRoute('product_info');

            } else {
                $this->addFlash('error', 'Stored product items can not be deleted!');
                return $this->redirectToRoute('product_info');
            }
        } else {
            $this->addFlash('error', 'Item not found!');
            return $this->redirectToRoute('product_info');
        }
    }
}
