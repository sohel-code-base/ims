<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'placeholder' => 'Product name'
                ]
            ])
            ->add('quantity', NumberType::class,[
                'attr' => [
                    'placeholder' => 'Product quantity'
                ]
            ])
            ->add('purchaseDate', TextType::class,[
                'attr' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'dd-mm-yyyy'
                ]
            ])
            ->add('category',EntityType::class,[
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('e')
                    ->orderBy('e.name', 'ASC');
                },
                'placeholder' => 'Category'
            ])
            ->add('productType', EntityType::class,[
                'class' => ProductType::class,
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $entityRepository){
                return $entityRepository->createQueryBuilder('e')
                    ->orderBy('e.name', 'ASC');
                },
                'placeholder' => 'Product Type'
            ])
            ->add('image', FileType::class,[
//                'help' => 'Product Image',
                'mapped' => false,
                'required' =>false
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
