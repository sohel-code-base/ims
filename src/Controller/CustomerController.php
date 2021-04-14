<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/customer/new", name="new_customer")
     * @param Request $request
     * @return Response
     */
    public function addCustomer(Request $request): Response
    {
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
}
