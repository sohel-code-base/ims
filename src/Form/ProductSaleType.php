<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Power;
use App\Entity\ProductPurchase;
use App\Entity\ProductSale;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('perPcsPrice')
//            ->add('power',EntityType::class,[
//                'required' => false,
//                'class' => Power::class,
//                'placeholder' => 'Select watt',
//                'choice_label' => 'watt',
//                'query_builder' => function(EntityRepository $repository){
//                return $repository->createQueryBuilder('e')
//                    ->orderBy('e.watt', 'ASC');
//                }
//            ])
            ->add('customer',EntityType::class,[
                'class' => Customer::class,
                'placeholder' => 'Select Customer',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->where('e.status = 1')
                    ->orderBy('e.name', 'ASC');
                },
            ])
            ->add('product',EntityType::class,[
                'class' => ProductPurchase::class,
                'placeholder' => 'Select Product',
                'choice_label' => function($productPurchase){
                    $product = $productPurchase->getProduct() ? $productPurchase->getProduct()->getName() : '';
                    $watt = $productPurchase->getPower() ? ' ---'.$productPurchase->getPower()->getWatt() . ' w' : '';
                return  $product . $watt;
                },
                'group_by' => 'product.subCategory.category.name',

                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('e')
                        ->join('e.product', 'product')
                        ->leftJoin('e.power', 'power')
                        ->where('e.status = 1')
                        ->andWhere('e.quantity > 0')
                        ->orderBy('product.name', 'ASC');
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
                    'data-width'=> '80',
//                    'checked' => 'checked'
                ],
                'label' => false,
            ])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('saleDate', TextType::class,[
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
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
