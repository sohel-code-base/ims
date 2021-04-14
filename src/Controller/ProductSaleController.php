<?php

namespace App\Controller;

use App\Entity\ProductSale;
use App\Form\ProductSaleType;
use App\Repository\CustomerRepository;
use App\Repository\PowerRepository;
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
     * @Route("/product/sale/new", name="new_sale", options={"expose"=true})
     * @param Request $request
     * @return Response
     */
    public function newSale(Request $request): Response
    {
        $productSale = new ProductSale();
        $form = $this->createForm(ProductSaleType::class, $productSale);

        return $this->render('product_sale/newSale.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/sale/store-record/ajax", name="store_new_sale_record", options={"expose"=true})
     * @param CustomerRepository $customerRepository
     * @param ProductPurchaseRepository $productPurchaseRepository
     * @param PowerRepository $powerRepository
     * @return JsonResponse
     */
    public function storeNewSaleRecord(CustomerRepository $customerRepository, ProductPurchaseRepository $productPurchaseRepository, PowerRepository $powerRepository)
    {
        $customerId = $_REQUEST['customerId'];
        $productId = $_REQUEST['product'];
        $quantity = $_REQUEST['quantity'];
        $perPiecePrice = $_REQUEST['perPiecePrice'];
        $watt = $_REQUEST['watt'];

        $findCustomer = $customerRepository->findOneBy(['id' => $customerId]);
        $findProduct = $productPurchaseRepository->findOneBy(['id' => $productId]);
        $findWatt = $powerRepository->findOneBy(['watt' => $watt]);

        if (!empty($findCustomer) && !empty($findProduct)){
            $productSale = new ProductSale();
            $em = $this->getDoctrine()->getManager();

            $productSale->setCustomer($findCustomer);
            $productSale->setProduct($findProduct);
            $productSale->setQuantity($quantity);
            $productSale->setPerPcsPrice($perPiecePrice);
            $productSale->setTotalPrice($quantity * $perPiecePrice);
            $productSale->setPower($findWatt ? $findWatt: null);
            $productSale->setCreatedAt(new \DateTime('now'));
            $productSale->setStatus(1);

            $em->persist($productSale);
            $em->flush();

            $prevQuantity = $findProduct->getQuantity();
            $findProduct->setQuantity($prevQuantity - $quantity);
            $em->persist($findProduct);
            $em->flush();

            return new JsonResponse('success');
        }else{
            return new JsonResponse('failed');
        }
    }

    /**
     * @Route("/sale/details", name="show_sale_details")
     * @param Request $request
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
            return $this->redirectToRoute('all_product_sale');
        }
    }

    /**
     * @Route("/sale/{id}/product/details", methods={"GET"}, name="new_sale_product_details", options={"expose"=true})
     * @param $id
     * @param ProductPurchaseRepository $repository
     * @return JsonResponse
     */
    public function getProductDetailsOnProductSelect($id, ProductPurchaseRepository $repository)
    {
        $findProduct = $repository->findOneBy(['id' => $id]);
        if ($findProduct){
            $returnData = [
                'id' => $findProduct->getId(),
                'purchasePrice' => $findProduct->getPurchasePrice(),
                'salePrice' => $findProduct->getSalePrice(),
                'quantity' => $findProduct->getQuantity(),
                'watt' => $findProduct->getPower() ? $findProduct->getPower()->getWatt():'',
            ];

            return new JsonResponse($returnData);
        }else{
            return new JsonResponse('failed!');
        }

    }

    /**
     * @Route("/sale/{id}/customer/details", name="new_sale_customer_details", options={"expose"=true})
     * @param $id
     * @param CustomerRepository $repository
     * @return JsonResponse
     */
    public function getCustomerDetails($id, CustomerRepository $repository)
    {
        $findCustomer = $repository->findOneBy(['id' => $id]);
        if ($findCustomer){
            $returnData = [
                'id' => $findCustomer->getId(),
                'phone' => $findCustomer->getPhone(),
                'address' => $findCustomer->getAddress(),
            ];
            return new JsonResponse($returnData);
        }else{
            return new JsonResponse('failed!');
        }
    }

}
