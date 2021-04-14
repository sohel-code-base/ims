<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Power;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'attr' => [
                    'autocomplete' => 'off'
                ]
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
//                    'checked' => 'checked'
                ],
                'label' => false,
            ])
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
            ->add('category', EntityType::class,[
                'class' => Category::class,
                'placeholder' => 'Select a Category',
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->where('e.status = 1')
                    ->orderBy('e.name','ASC');
                }
            ])
            ->add('subCategory', EntityType::class,[
                'required' => false,
                'class' => SubCategory::class,
                'placeholder' => 'Select a Sub-Category',
                'choice_label' => 'name',
                'group_by' => 'category.name',
                'query_builder' => function(EntityRepository $repository){
                    return $repository->createQueryBuilder('e')
                        ->where('e.status = 1')
                        ->orderBy('e.name','ASC');
                }
            ])
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
