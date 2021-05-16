<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customer", name="all_customer")
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
     * @Route("/customer/new/{from}", defaults={"from" = null}, methods={"GET","POST"}, name="new_customer")
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
     * @Route("/{id}/customer/details", name="new_sale_customer_details", options={"expose"=true})
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
