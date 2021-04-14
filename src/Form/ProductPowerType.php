<?php

namespace App\Form;

use App\Entity\Power;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPowerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('watt')
            ->add('status', CheckboxType::class,[
                'required' => false,
                'attr' => [
                    'data-toggle' => 'toggle',
                    'data-style' => 'slow',
                    'data-offstyle' => 'danger',
                    'data-onstyle'=> 'success',
                    'data-on' => 'Enabled',
                    'data-off'=> 'Disabled',
                    'data-width'=> '150',
//                    'checked' => 'checked'
                ],
                'label' => false,
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Power::class,
        ]);
    }
}
