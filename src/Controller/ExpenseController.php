<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\FilterType;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/new", name="new_expense")
     */
    public function newExpense(Request $request)
    {
        $data = $request->request->all();

        $expense = new Expense();
        $expense->setType($data['expenseType']);
        $expense->setExpenseDate(new \DateTime($data['expenseDate']));
        $expense->setAmount($data['expenseAmount']);
        $expense->setCreatedAt(new \DateTime('now'));
        $expense->setStatus(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($expense);
        $em->flush();

        return new JsonResponse(['status' => 200]);

    }
}
