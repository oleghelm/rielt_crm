<?php

namespace AppBundle\Form;

use AppBundle\Entity\Param;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\TextType;//for chices


class ParamFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class,['label'=>'Назва'])
                ->add('sort',TextType::class,['label'=>'Порядок'])
                ->add('multiple',ChoiceType::class, [
                    'label' => 'Вибір більше одного параметру',
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ]
                ])
                ->add('useInFilter',ChoiceType::class, [
                    'label' => 'Використовувати в фільтрі',
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ]
                ])
                ->add('type',ChoiceType::class, [
                    'label' => 'Тип',
                    'choices' => [
                        'Параметр' => 'select',
//                        'Вибір кількох'  => 'select_multiple',
                        'Текст'  => 'text',
                        'Число'  => 'integer',
                        'Діапазон'  => 'diapazon',
                    ]
                ])
                ->add('filter',ChoiceType::class, [
                    'label' => 'В якому фільтрі виводити',
                    'multiple' => true,
                    'choices' => [
                        'Продаж' => 'simple_sale',
                        'Оренда'  => 'simple_rent',
                        'Комерція продаж'  => 'comercial_sale',
                        'Комерція оренда'  => 'comercial_rent',
                    ]
                ])
                ->add('detail',ChoiceType::class, [
                    'label' => "Для яких об'єктів виводити",
                    'multiple' => true,
                    'choices' => [
                        'Продаж' => 'simple_sale',
                        'Оренда'  => 'simple_rent',
                        'Комерція продаж'  => 'comercial_sale',
                        'Комерція оренда'  => 'comercial_rent',
                    ]
                ])
                ->add('exportName',TextType::class,['label'=>'Значення в вигрузці'])
                ->add('useInExport',ChoiceType::class, [
                    'label' => 'Використовувати у вигрузці',
                    'expanded' => true,
                    'choices' => [
                        'Ні'  => false,
                        'Так' => true,
                    ]
                ])
                ->add('basicParam',ChoiceType::class, [
                    'label' => 'Базовий параметр',
                    'expanded' => true,
                    'choices' => [
                        'Ні'  => false,
                        'Так' => true,
                    ]
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Param'
        ]);
    }
}