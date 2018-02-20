<?php

namespace AppBundle\Form;

use AppBundle\Entity\Param;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices


class ParamFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name')
                ->add('sort')
                ->add('multiple',ChoiceType::class, [
                    'label' => 'Вибір більше одного параметру',
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ]
                ])
                ->add('useInFilter',ChoiceType::class, [
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ]
                ])
                ->add('type',ChoiceType::class, [
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
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Param'
        ]);
    }
}