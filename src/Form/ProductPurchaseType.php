<?php

namespace App\Form;

use App\Entity\Power;
use App\Entity\Product;
use App\Entity\ProductPurchase;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPurchaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('purchasePrice')
            ->add('salePrice')
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
            ->add('purchaseDate', TextType::class,[
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('product', EntityType::class,[
                'class' => Product::class,
                'placeholder' => 'Select Product',
                'choice_label' => 'proName',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('e')
                        ->where('e.status = 1')
                        ->orderBy('e.id','DESC');
                }
            ])
            ->add('proPower', EntityType::class,[
                'required' => false,
                'class' => Power::class,
                'placeholder' => 'Select Power',
                'choice_label' => 'watt',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('e')
                        ->where('e.status = 1')
                        ->orderBy('e.watt','ASC');
                }
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPurchase::class,
        ]);
    }
}
