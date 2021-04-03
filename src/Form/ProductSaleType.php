<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\ProductPurchase;
use App\Entity\ProductSale;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('customer',EntityType::class,[
                'class' => Customer::class,
                'placeholder' => 'Select Customer',
                'choice_label' => 'cusName',
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->where('e.status = 1')
                    ->orderBy('e.cusName', 'ASC');
                },
            ])
            ->add('product',EntityType::class,[
                'class' => ProductPurchase::class,
                'placeholder' => 'Select Product',
                'choice_label' => 'product.proName',
                'group_by' => 'product.proCategory.catName',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('e')
                        ->join('e.product', 'product')
                        ->where('e.status = 1')
                        ->orderBy('product.proName', 'ASC');
                },
            ])
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
                    'checked' => 'checked'
                ],
                'label' => false,
            ])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductSale::class,
        ]);
    }
}
