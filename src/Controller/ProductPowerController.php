<?php

namespace App\Controller;

use App\Entity\Power;
use App\Form\ProductPowerType;
use App\Repository\PowerRepository;
use App\Repository\ProductPurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductPowerController
 * @package App\Controller
 * @Route("/product/power")
 */
class ProductPowerController extends AbstractController
{
    /**
     * @Route("/", name="all_power")
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
     * @Route("/new", name="product_new_power")
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

    /**
     * @param $id
     * @param Request $request
     * @param PowerRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/{id}/edit", name="edit_product_power")
     */
    public function editPower($id, Request $request, PowerRepository $repository)
    {
        $findPower = $repository->find($id);
        $form = $this->createForm(ProductPowerType::class, $findPower)->remove('createdAt')->remove('updatedAt');
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $findPower->setUpdatedAt(new \DateTime('now'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($findPower);
            $em->flush();

            $this->addFlash('update', 'Power updated!');
            return $this->redirectToRoute('all_power');
        }
        return $this->render('product_power/editPower.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param $id
     * @param PowerRepository $powerRepository
     * @param ProductPurchaseRepository $purchaseRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/delete", name="delete_product_power")
     */
    public function deletePower($id, PowerRepository $powerRepository, ProductPurchaseRepository $purchaseRepository)
    {
        $findPower = $powerRepository->find($id);
        if ($findPower){
            $findPurchaseProduct = $purchaseRepository->findBy(['power' => $findPower]);
            if (!$findPurchaseProduct){
                $em = $this->getDoctrine()->getManager();
                $em->remove($findPower);
                $em->flush();
                $this->addFlash('success', 'Power deleted!');
                return $this->redirectToRoute('all_power');
            } else {
                $this->addFlash('error', 'Stored product powers can not be deleted!');
                return $this->redirectToRoute('all_power');
            }
        } else {
            $this->addFlash('error', 'Power not found!');
            return $this->redirectToRoute('all_power');
        }
    }
}
