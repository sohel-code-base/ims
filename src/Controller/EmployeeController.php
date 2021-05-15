<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class EmployeeController
 * @package App\Controller
 * @Route("/employee")
 */
class EmployeeController extends AbstractController
{
    /**
     * @Route("/", name="all_employee")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        $employees = $userRepository->getActiveEmployee();

        return $this->render('employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            dd($form->getData());

            $userPhoto = $form->get('photo')->getData();
            $userSignature = $form->get('signature')->getData();

            if ($userPhoto){
                $fileName = $form->get('fullName')->getData() . '-photo' . '.' . $userPhoto->getClientOriginalExtension();
                $userPhoto->move($this->getParameter('uploads_dir') . '/photo/', $fileName);
                $user->setPhoto($fileName);
            }else{
                $user->setPhoto('user-avt.png');
            }

            if ($userSignature){
                $fileName = $form->get('username')->getData() . '-signature' . '.' . $userSignature->getClientOriginalExtension();
                $userSignature->move($this->getParameter('uploads_dir') . '/signature/', $fileName);
                $user->setSignature($fileName);
            }
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setCreatedAt(new \DateTime('now'));
            $user->setStatus(1);
            $user->setJoiningDate(new \DateTime($form->get('joiningDate')->getData()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
/*
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );*/
            $this->addFlash('success', 'New Employee Added!');
            return $this->redirectToRoute('all_employee');
        }

        return $this->render('employee/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
