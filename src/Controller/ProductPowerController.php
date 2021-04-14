<?php

namespace App\Controller;

use App\Entity\Power;
use App\Form\ProductPowerType;
use App\Repository\PowerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductPowerController extends AbstractController
{
    /**
     * @Route("/product/power", name="all_power")
     * @param PowerRepository $repository
     * @return Response
     */
    public function index(PowerRepository $repository): Response
    {
        $power = $repository->findAll();
        return $this->render('product_power/index.html.twig', [
            'power' => $power,
        ]);
    }

    /**
     * @Route("/product/power/new", name="product_new_power")
     * @param Request $request
     * @return Response
     */
    public function addPower(Request $request): Response
    {

        $power = new Power();
        $form = $this->createForm(ProductPowerType::class, $power);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $power->setCreatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($power);
            $em->flush();

            $this->addFlash('success', 'Power added!');
            return $this->redirectToRoute('product_new_power');
        }
        return $this->render('product_power/addPower.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
