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
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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

    /**
     * @Route("/{id}/edit", name="edit_employee")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param $id
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function edit(Request $request, UserRepository $userRepository, $id, UserPasswordEncoderInterface $passwordEncoder)
    {
        $findEmployee = $userRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(RegistrationFormType::class, $findEmployee);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $joiningDate = $form->get('joiningDate')->getData();
            $findEmployee->setJoiningDate(new \DateTime($joiningDate));

            $userPhoto = $form->get('photo')->getData();
            $userSignature = $form->get('signature')->getData();

            if ($userPhoto){
                $fileName = $form->get('fullName')->getData() . '-photo' . '.' . $userPhoto->getClientOriginalExtension();
                move_uploaded_file($userPhoto->getPathname(), $this->getParameter('uploads_dir') . '/photo/' . $fileName);
                $findEmployee->setPhoto($fileName);
            }

            if ($userSignature){
                $fileName = $form->get('username')->getData() . '-signature' . '.' . $userSignature->getClientOriginalExtension();
                move_uploaded_file($userSignature->getPathname(), $this->getParameter('uploads_dir') . '/signature/' . $fileName);
                $findEmployee->setSignature($fileName);
            }
            // encode the plain password
            $findEmployee->setPassword(
                $passwordEncoder->encodePassword(
                    $findEmployee,
                    $form->get('password')->getData()
                )
            );
            $findEmployee->setUpdatedAt(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($findEmployee);
            $entityManager->flush();
            $this->addFlash('update', 'Employee details has been updated!');

            return $this->redirectToRoute('all_employee');
        }

        $joiningDateToString = $form->get('joiningDate')->getData()->format('d-m-Y');
        $form->get('joiningDate')->setData($joiningDateToString);

        return $this->render('employee/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete_employee")
     * @param $id
     * @param UserRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete($id, UserRepository $repository)
    {
        $findEmployee = $repository->findOneBy(['id' => $id]);
        if ($findEmployee){
            $findEmployee->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($findEmployee);
            $em->flush();
            $this->addFlash('success', 'Employee deleted successfully!');
            return $this->redirectToRoute('all_employee');
        }else{
            $this->addFlash('error', 'Employee not found!');
            return $this->redirectToRoute('all_employee');
        }
    }

    /**
     * @Route("/profile", name="employee_profile")
     */
    public function myProfile()
    {
        return $this->render('employee/myProfile.html.twig');
    }
}
