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
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductSaleController
 * @package App\Controller
 * @Route("/product/sale")
 */
class ProductSaleController extends AbstractController
{
    /**
     * @Route("/", name="all_product_sale")
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
     * @Route("/new", name="new_sale", options={"expose"=true})
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
     * @Route("/store-record/ajax", name="store_new_sale_record", options={"expose"=true})
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
     * @Route("/details", name="show_sale_details")
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
     * @Route("/{id}/delete", name="delete_sale_record")
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
     * @Route("/product/collect", name="collect_product_customer_and_sale_date", options={"expose"=true})
     * @param ProductSaleDetailsRepository $repository
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
     * @Route("/product/remove", name="remove_product_from_sale_list", options={"expose"=true})
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
     * @Route("/payment", name="sale_payment")
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

    /**
     * @param Request $request
     * @param ProductSaleRepository $repository
     * @return Response
     * @Route("/collect/due-amount", name="collect_due_amount")
     */
    public function updateDueAmount(Request $request, ProductSaleRepository $repository)
    {
        $form = $this->createFormBuilder()
            ->add('saleId', EntityType::class,[
                'class' => ProductSale::class,
                'choice_label' => 'id',
                'placeholder' => 'Select receipt no.',
                'query_builder' => function(EntityRepository $er){
                return $er->createQueryBuilder('e')
                    ->where('e.dueAmount > 0')
                    ->orderBy('e.id','ASC');
                }
            ])
            ->add('dueAmount',TextType::class,[
                'attr' => [
                    'disabled' => 'disabled'
                ]
            ])
            ->add('Submit', SubmitType::class,[
                'attr' => [
                    'class' => 'btn-block btn-flat btn-facebook'
                ]
            ])
            ->getForm()
            ;
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $findSale = $repository->find($form->get('saleId')->getData());

            if ($findSale){
                $findSale->setDueAmount($findSale->getDueAmount() - $form->get('dueAmount')->getData());
                $findSale->setUpdatedAt(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($findSale);
                $em->flush();
                $this->addFlash('update','Database updated!');
                return $this->redirectToRoute('collect_due_amount');
            }else{
                $this->addFlash('update','Something Wrong!');
                return $this->redirectToRoute('collect_due_amount');
            }
        }
        return $this->render('product_sale/updateDueAmount.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/{saleId}/due/sale-summary", name="due_amount_sale_summary", options={"expose"=true})
     * @param $saleId
     * @param Request $request
     * @param ProductSaleRepository $repository
     * @return JsonResponse
     */
    public function dueAmountSaleSummary($saleId, Request $request, ProductSaleRepository $repository)
    {
        $details = $repository->getSaleSummery($saleId);
        $details['orderDate'] = $details['orderDate']->format('d-m-Y');

        return new JsonResponse([
            'status' => 200,
            'details' => $details
        ]);
    }

}
