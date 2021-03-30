<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('proName')
            ->add('purchasePrice')
            ->add('quantity')
            ->add('salePrice')
            ->add('purchaseDate', TextType::class)
            ->add('status')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('proCategory')
            ->add('proSubCategory')
            ->add('proPower')
            ->add('Submit', SubmitType::class,[
                'attr' => [
                    'class' => 'btn-block btn-flat btn-success'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
