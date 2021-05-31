<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Repository\ProductSaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CustomerController
 * @package App\Controller
 * @Route("/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="all_customer")
     * @param CustomerRepository $repository
     * @return Response
     */
    public function index(CustomerRepository $repository): Response
    {
        $allCustomers = $repository->getAllCustomers();
        return $this->render('customer/index.html.twig', [
            'allCustomers' => $allCustomers,
        ]);
    }

    /**
     * @Route("/new/{from}", defaults={"from" = null}, methods={"GET","POST"}, name="new_customer")
     * @param Request $request
     * @param $from
     * @return Response
     */
    public function addCustomer(Request $request, $from): Response
    {
        if ($from == 'modal'){
            $formData = $request->request->all();
            $customer = new Customer();
            $customer->setName($formData['customerName']);
            $customer->setPhone($formData['customerPhone']);
            $customer->setAddress($formData['customerAddress']);
            $customer->setStatus(1);
            $customer->setCreatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            return new JsonResponse(['status' => 200]);
            die();
        }

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $customer->setCreatedAt(new \DateTime('now'));
            $em->persist($customer);
            $em->flush();
            $this->addFlash('success', 'New customer added into Database!');
            return $this->redirectToRoute('new_customer');
        }


        return $this->render('customer/addCustomer.htm.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_customer")
     * @param Request $request
     * @param $id
     * @param CustomerRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editCustomer(Request $request, $id, CustomerRepository $repository)
    {
        $findCustomer = $repository->find($id);
        if ($findCustomer){

            $form = $this->createForm(CustomerType::class, $findCustomer)->remove('createdAt')->remove('updatedAt');
            $form->handleRequest($request);

            if($form->isSubmitted()){
                $em = $this->getDoctrine()->getManager();
                $findCustomer->setUpdatedAt(new \DateTime('now'));
                $em->persist($findCustomer);
                $em->flush();
                $this->addFlash('success', 'Customer details updated!');
                return $this->redirectToRoute('all_customer');
            }

            return $this->render('customer/editCustomer.htm.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}/delete", name="delete_customer")
     * @param $id
     * @param CustomerRepository $repository
     * @param ProductSaleRepository $saleRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCustomer($id, CustomerRepository $repository, ProductSaleRepository $saleRepository)
    {
        $findCustomer = $repository->find($id);
        if ($findCustomer){
            $findCustomerInSaleList = $saleRepository->findBy(['customer' => $findCustomer]);
            if (!$findCustomerInSaleList){
                $em = $this->getDoctrine()->getManager();
                $em->remove($findCustomer);
                $em->flush();

                $this->addFlash('success', 'Customer has been deleted!');
                return $this->redirectToRoute('all_customer');
            } else {}
            $this->addFlash('error', 'Customers who have already purchased product cannot be deleted!');
            return $this->redirectToRoute('all_customer');
        } else {
            $this->addFlash('error', 'Customer not found!');
            return $this->redirectToRoute('all_customer');
        }
    }

    /**
     * @Route("/{id}/details", name="new_sale_customer_details", options={"expose"=true})
     * @param $id
     * @param CustomerRepository $repository
     * @return JsonResponse
     */
    public function customerDetails($id, CustomerRepository $repository)
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
