<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSubCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subCatName',TextType::class,[
                'attr' =>[
                    'autocomplete' => 'off'
                ]
            ])
            ->add('category',EntityType::class,[
                'placeholder' => 'Select a Category',
                'class' => Category::class,
                'choice_label' => 'catName',
                'query_builder' => function(EntityRepository $repository){
                return $repository->createQueryBuilder('e')
                    ->where('e.status = 1')
                    ->orderBy('e.catName', 'ASC');
                }
            ])
            ->add('createdAt')
            ->add('updatedAt')
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
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SubCategory::class,
        ]);
    }
}
