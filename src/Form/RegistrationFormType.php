<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class, [
                'help' => 'Note: Username & Password must be used for login!'
            ])
            ->add('phone')
            ->add('address')
            ->add('roles', ChoiceType::class, [
                'choices' => [
//                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    'Sales Representative (SR)' => 'ROLE_SR',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('designation')
            ->add('signature', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('joiningDate', TextType::class)
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false,
            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => false,
//                'constraints' => [
//                    new IsTrue([
//                        'message' => 'You should agree to our terms.',
//                    ]),
//                ],
//            ])
            ->add('jobStatus', CheckboxType::class,[
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-style' => 'slow',
                    'data-offstyle' => 'danger',
                    'data-onstyle'=> 'success',
                    'data-on' => 'Permanent',
                    'data-off'=> 'Temporary',
//                    'data-width'=> '150',
//                    'checked' => 'checked'
                ],
//                'label' => false,
            ])
            ->add('Save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-flat btn-primary w-20'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
