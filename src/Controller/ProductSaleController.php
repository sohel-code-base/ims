<?php

namespace App\Controller;

use App\Entity\ProductSale;
use App\Form\ProductSaleType;
use App\Repository\ProductPurchaseRepository;
use App\Repository\ProductSaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductSaleController extends AbstractController
{
    /**
     * @Route("/product/sale", name="all_product_sale")
     * @param ProductSaleRepository $repository
     * @return Response
     */
    public function index(ProductSaleRepository $repository): Response
    {
        $allSales = $repository->getSaleRecords();
        return $this->render('product_sale/index.html.twig', [
            'allSales' => $allSales,
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

            $quantity = $form->get('quantity')->getData();
            $rate = $form->get('perPcsPrice')->getData();

            $productSale->setTotalPrice($quantity * $rate);
            $productSale->setCreatedAt(new \DateTime('now'));
            $em->persist($productSale);
            $em->flush();

            $purchaseProduct = $form->get('product')->getData();
            $purchaseProduct->setQuantity($purchaseProduct->getQuantity()-$quantity);
            $em->persist($purchaseProduct);
            $em->flush();

            $this->addFlash('success', 'Sale successful!');
            return $this->redirectToRoute('all_product_sale');
        }
        return $this->render('product_sale/newSale.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sale/details", name="show_sale_details")
     * @param ProductSaleRepository $repository
     * @return Response
     * @throws \Exception
     */
    public function showSaleDetails(Request $request, ProductSaleRepository $repository)
    {
        $requestData = $request->query->all();
        $orderDate = new \DateTime($requestData['orderDate']);
        $filterBy['customerId'] = $requestData['customerId'];
        $filterBy['orderDate'] = $orderDate;
        $saleDetails = $repository->getSalesRecordByCustomerAndDate($filterBy);

        return new JsonResponse($saleDetails);
    }

    /**
     * @Route("/sale/{id}/delete", name="delete_sale_record")
     * @param $id
     * @param ProductSaleRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteRecord($id, ProductSaleRepository $repository)
    {
        $findRecord = $repository->find($id);
        if ($findRecord){
            $em = $this->getDoctrine()->getManager();
            $em->remove($findRecord);
            $em->flush();
            $this->addFlash('success', 'Record has been deleted successfully!');
            return $this->redirectToRoute('all_product_sale');
        }else{
            $this->addFlash('error','Record not found!');
            return $this->redirectToRoute('delete_sale_record');
        }
    }
}
