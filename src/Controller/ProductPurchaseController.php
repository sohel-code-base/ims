<?php

namespace App\Controller;

use App\Entity\ProductPurchase;
use App\Entity\ProductPurchaseArchive;
use App\Form\FilterType;
use App\Form\ProductPurchaseType;
use App\Repository\ProductPurchaseArchiveRepository;
use App\Repository\ProductPurchaseRepository;
use App\Repository\ProductSaleDetailsRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductPurchaseController
 * @package App\Controller
 * @Route("/")
 */
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
     * @Route("product/purchase/archive", name="product_purchase_archive")
     * @param Request $request
     * @param $mode
     * @param ProductPurchaseArchiveRepository $repository
     * @return Response
     * @throws \Exception
     */
    public function purchaseArchive(Request $request, ProductPurchaseArchiveRepository $repository)
    {
        $filterBy = new \DateTime('now');

        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted()){
            $filterBy = new \DateTime($filterForm->get('monthYear')->getData());
        }
        $records = $repository->getProductPurchaseByMonth($filterBy);

        return $this->render('product_purchase/productPurchaseArchive.html.twig',[
            'filterForm' => $filterForm->createView(),
            'records' => $records,
            'filterBy' => $filterBy,
        ]);
    }

    /**
     * @param Request $request
     * @param ProductPurchaseArchiveRepository $repository
     * @throws \Exception
     * @Route("product/purchase/archive/pdf", name="product_purchase_archive_pdf")
     */
    public function purchaseArchivePdf(Request $request, ProductPurchaseArchiveRepository $repository)
    {
        $filterBy = new \DateTime($request->query->get('month'));

        $records = $repository->getProductPurchaseByMonth($filterBy);

            $options = new Options();
            $options->set('defaultFont', 'Courier');

            $dompdf = new Dompdf($options);
            $html = $this->renderView('product_purchase/pdf-productPurchaseArchive.html.twig',[
                'records' => $records,
            ]);
            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream($filterBy->format('F-Y') . 'purchase-archive.pdf', [
                "Attachment" => false
            ]);
            die();
    }

    /**
     * @Route("product/purchase/new", name="product_purchase")
     * @param Request $request
     * @param ProductPurchaseRepository $purchaseRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function productPurchase(Request $request, ProductPurchaseRepository $purchaseRepository)
    {
        $productPurchase = new ProductPurchase();
        $productPurchaseArchive = new ProductPurchaseArchive();
        $form = $this->createForm(ProductPurchaseType::class, $productPurchase);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();

//            dd($form->get('purchaseDate')->getData());
            $purchaseDate = new \DateTime($form->get('purchaseDate')->getData());
            $product = $form->get('product')->getData();
            $purchaseQuantity = $form->get('quantity')->getData();
            $purchasePrice = $form->get('purchasePrice')->getData();
            $salePrice = $form->get('salePrice')->getData();
            $power = $form->get('power')->getData();

            $productPurchaseArchive->setProduct($product);
            $productPurchaseArchive->setPower($power);
            $productPurchaseArchive->setQuantity($purchaseQuantity);
            $productPurchaseArchive->setPurchasePrice($purchasePrice);
            $productPurchaseArchive->setSalePrice($salePrice);
            $productPurchaseArchive->setPurchaseDate($purchaseDate);
            $productPurchaseArchive->setStatus(1);
            $productPurchaseArchive->setCreatedAt(new \DateTime('now'));
            $em->persist($productPurchaseArchive);
            $em->flush();

            $findExistingProduct = $purchaseRepository->findOneBy(['product' => $product, 'power' => $power]);

            if ($findExistingProduct){
                $preQuantity = $findExistingProduct->getQuantity();


                $findExistingProduct->setPurchasePrice($purchasePrice);
                $findExistingProduct->setSalePrice($salePrice);
                $findExistingProduct->setQuantity($preQuantity + $purchaseQuantity);
                $findExistingProduct->setPurchaseDate($purchaseDate);
                $findExistingProduct->setPower($power ? $power: null);
                $findExistingProduct->setUpdatedAt(new \DateTime('now'));
                $em->persist($findExistingProduct);
                $em->flush();
                $this->addFlash('update', 'Product Quantity Updated!');
                return $this->redirectToRoute('product_purchase');
            }
            $productPurchase->setPurchaseDate($purchaseDate);
            $productPurchase->setCreatedAt(new \DateTime('now'));
            $em->persist($productPurchase);
            $em->flush();


            $this->addFlash('success', 'New Product added into Database!');
            return $this->redirectToRoute('product_purchase');
        }
        return $this->render('product_purchase/purchaseProduct.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("product/purchase/{id}/delete", name="delete_purchase_product")
     * @param $id
     * @param ProductPurchaseRepository $purchaseRepository
     * @param ProductSaleDetailsRepository $saleDetailsRepository
     * @param ProductPurchaseArchiveRepository $archiveRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePurchaseProduct($id, ProductPurchaseRepository $purchaseRepository, ProductSaleDetailsRepository $saleDetailsRepository, ProductPurchaseArchiveRepository $archiveRepository)
    {
        $findPurchaseProduct = $purchaseRepository->find($id);
        if ($findPurchaseProduct){
            $findSale = $saleDetailsRepository->findOneBy(['product' => $findPurchaseProduct]);
            if(! $findSale){
                $em = $this->getDoctrine()->getManager();

                $findArchive = $archiveRepository->findOneBy(['product' => $findPurchaseProduct->getProduct(), 'purchaseDate' => $findPurchaseProduct->getPurchaseDate()]);
                if ($findArchive){
                    $em->remove($findArchive);
                }
                $em->remove($findPurchaseProduct);
                $em->flush();
                $this->addFlash('success','Product has been deleted!');
                return $this->redirectToRoute('all_purchase_product');
            }else{
                $this->addFlash('error','You can not delete the product that has been sold!');
                return $this->redirectToRoute('all_purchase_product');
            }
        } else {
            $this->addFlash('error','Not Found!');
            return $this->redirectToRoute('all_purchase_product');
        }

    }

    /**
     * @Route("product/purchase/{id}/details", methods={"GET"}, name="new_sale_product_details", options={"expose"=true})
     * @param $id
     * @param ProductPurchaseRepository $repository
     * @return JsonResponse
     */
    public function getProductDetailsOnProductSelect($id, ProductPurchaseRepository $repository)
    {
        $findProduct = $repository->find($id);
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
}
