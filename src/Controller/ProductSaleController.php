<?php

namespace App\Controller;

use App\Entity\ProductSale;
use App\Entity\ProductSaleDetails;
use App\Form\ProductSaleType;
use App\Repository\CustomerRepository;
use App\Repository\PowerRepository;
use App\Repository\ProductPurchaseRepository;
use App\Repository\ProductSaleDetailsRepository;
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
     * @Route("/product/sale/new", name="new_sale", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function newSaleView(Request $request): Response
    {
        $form = $this->createForm(ProductSaleType::class);

        return $this->render('product_sale/newSale.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/sale/store-record/ajax", name="store_new_sale_record", options={"expose"=true})
     * @param CustomerRepository $customerRepository
     * @param ProductPurchaseRepository $productPurchaseRepository
     * @param PowerRepository $powerRepository
     * @param ProductSaleRepository $productSaleRepository
     * @return JsonResponse
     * @throws \Exception
     */
    public function storeNewSaleRecord(CustomerRepository $customerRepository, ProductPurchaseRepository $productPurchaseRepository, PowerRepository $powerRepository, ProductSaleRepository $productSaleRepository)
    {
        $customerId = $_REQUEST['customerId'];
        $productPurchaseId = $_REQUEST['productPurchaseId'];
        $quantity = $_REQUEST['quantity'];
        $perPiecePrice = $_REQUEST['perPiecePrice'];
        $saleDate = new \DateTime($_REQUEST['saleDate']);

        $findCustomer = $customerRepository->findOneBy(['id' => $customerId]);
        $findProduct = $productPurchaseRepository->findOneBy(['id' => $productPurchaseId]);

        if (!empty($findCustomer) && !empty($findProduct)){
            $em = $this->getDoctrine()->getManager();

            $findSale = $productSaleRepository->findOneBy(['customer' => $findCustomer, 'saleDate' => $saleDate]);
            if ($findSale){
                $saleDetails = new ProductSaleDetails();
                $saleDetails->setSale($findSale);
                $saleDetails->setProduct($findProduct);
                $saleDetails->setQuantity($quantity);
                $saleDetails->setPerPcsPrice($perPiecePrice);

                $findSale->addProductSaleDetail($saleDetails);
                $findSale->setTotalPrice($findSale->getTotalPrice() + ($quantity * $perPiecePrice));
                $findSale->setDueAmount($findSale->getDueAmount() + ($quantity * $perPiecePrice));
                $em->persist($findSale);
                $em->flush();
            }else{
                $productSale = new ProductSale();
                $productSale->setCustomer($findCustomer);
                $productSale->setTotalPrice($quantity * $perPiecePrice);
                $productSale->setDueAmount($quantity * $perPiecePrice);
                $productSale->setSaleDate($saleDate);
                $productSale->setEmployee($this->getUser());
                $productSale->setCreatedAt(new \DateTime('now'));
                $productSale->setStatus(1);

                $saleDetails = new ProductSaleDetails();
                $saleDetails->setSale($productSale);
                $saleDetails->setProduct($findProduct);
                $saleDetails->setQuantity($quantity);
                $saleDetails->setPerPcsPrice($perPiecePrice);

                $productSale->addProductSaleDetail($saleDetails);
                $em->persist($productSale);
                $em->flush();
            }

            $prevQuantity = $findProduct->getQuantity();
            $findProduct->setQuantity($prevQuantity - $quantity);
            $em->persist($findProduct);
            $em->flush();

            $returnData = [
                'status' => 'success',
                'saleId' => $saleDetails->getSale()->getId(),
                'dueAmount' => $saleDetails->getSale()->getDueAmount(),
                'productName' => $saleDetails->getProduct()->getProduct()->getName(),
                'quantity' => $saleDetails->getQuantity(),
                'perPiecePrice' => $saleDetails->getPerPcsPrice(),
                'watt' => $saleDetails->getProduct()->getPower() ? $saleDetails->getProduct()->getPower()->getWatt() : '',
                'price' => $quantity * $perPiecePrice,
                'totalPrice' => $saleDetails->getSale()->getTotalPrice(),
            ];
            return new JsonResponse($returnData);
        }else{
            return new JsonResponse('failed');
        }
    }

    /**
     * @Route("/sale/details", name="show_sale_details")
     * @param Request $request
     * @param ProductSaleDetailsRepository $repository
     * @return Response
     * @throws \Exception
     */
    public function showSaleDetails(Request $request, ProductSaleDetailsRepository $repository)
    {
        $requestData = $request->query->all();
        $saleDate = new \DateTime($requestData['orderDate']);
        $customerId = $requestData['customerId'];

        $saleDetails = $repository->getProductByCustomerAndSaleDate($customerId, $saleDate);

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
            return $this->redirectToRoute('all_product_sale');
        }
    }


    /**
     * @Route("/sale/product/collect", name="collect_product_customer_and_sale_date", options={"expose"=true})
     * @param ProductSaleRepository $repository
     * @return JsonResponse
     * @throws \Exception
     */
    public function collectProductByCustomerAndSaleDate(ProductSaleDetailsRepository $repository)
    {
        $customerId = $_REQUEST['customerId'];
        $saleDate = new \DateTime($_REQUEST['saleDate']);

        $findRecords = $repository->getProductByCustomerAndSaleDate($customerId, $saleDate);

        return new JsonResponse($findRecords);

    }

    /**
     * @Route("/sale/product/remove", name="remove_product_from_sale_list", options={"expose"=true})
     * @param ProductSaleDetailsRepository $saleDetailsRepository
     * @param ProductSaleRepository $saleRepository
     * @param ProductPurchaseRepository $purchaseRepository
     * @return JsonResponse
     */
    public function removeProductFromSaleList(ProductSaleDetailsRepository $saleDetailsRepository, ProductSaleRepository $saleRepository, ProductPurchaseRepository $purchaseRepository)
    {
        $saleId = $_REQUEST['saleId'];
        $productPurchaseId = $_REQUEST['productPurchaseId'];
//        $saleDate = new \DateTime($_REQUEST['saleDate']);

        $findProduct = $saleDetailsRepository->findOneBy(['sale' => $saleId, 'product' => $productPurchaseId]);

        if ($findProduct){
            $em = $this->getDoctrine()->getManager();
            $price = $findProduct->getQuantity() * $findProduct->getPerPcsPrice();

            $findSale = $saleRepository->findOneBy(['id' => $findProduct->getSale()]);
            if (($findSale)){
                $findSale->setTotalPrice($findSale->getTotalPrice() - $price);
                $findSale->setDueAmount($findSale->getDueAmount() - $price);
                $em->persist($findSale);
            }

            $findPurchaseProduct = $purchaseRepository->findOneBy(['id' => $productPurchaseId]);
            if ($findPurchaseProduct){
                $findPurchaseProduct->setQuantity($findPurchaseProduct->getQuantity() + $findProduct->getQuantity());
                $em->persist($findPurchaseProduct);
            }

            $em->remove($findProduct);
            $em->flush();

            if ($findSale->getTotalPrice() == 0){
                $em->remove($findSale);
                $em->flush();
            }

            return new JsonResponse([
                'status' => 200,
                'totalPrice' => $findSale->getTotalPrice(),
                'dueAmount' => $findSale->getDueAmount(),
//                'productCount' => $findSale->getProductSaleDetails()->count(),
            ]);
        }

        return new JsonResponse('failed');
    }

    /**
     * @Route("/sale/payment", name="sale-payment")
     * @param ProductSaleRepository $saleRepository
     * @return JsonResponse
     */
    public function payment(ProductSaleRepository $saleRepository)
    {
        $saleId = $_REQUEST['saleId'];
        $payAmount = $_REQUEST['payAmount'];

        $findSale = $saleRepository->findOneBy(['id' => $saleId]);
        $em = $this->getDoctrine()->getManager();

        if ($findSale->getDueAmount() == 0){
            $findSale->setDueAmount($findSale->getTotalPrice() - $payAmount);

        }else{
            $findSale->setDueAmount($findSale->getDueAmount() - $payAmount);
        }
        $em->persist($findSale);
        $em->flush();
        return new JsonResponse([
            'status' => 200,
            'dueAmount' => $findSale->getDueAmount(),
        ]);

    }

}
