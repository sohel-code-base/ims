<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExpenseController
 * @package App\Controller
 * @Route("/expense")
 */
class ExpenseController extends AbstractController
{
    /**
     * @Route("/", name="expense_list")
     * @param Request $request
     * @param ExpenseRepository $repository
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, ExpenseRepository $repository): Response
    {
        $filterBy = new \DateTime('now');

        $filterForm = $this->createForm(FilterType::class);
        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted()){
            $filterBy = new \DateTime('01-'.$filterForm->get('monthYear')->getData());
        }
        $records = [];


        return $this->render('expense/index.html.twig', [
            'filterForm' => $filterForm->createView(),
            'records' => $records,
            'filterBy' => $filterBy,
        ]);
    }

    /**
     * @Route("/new", name="new_expense")
     */
    public function newExpense()
    {


    }
}
