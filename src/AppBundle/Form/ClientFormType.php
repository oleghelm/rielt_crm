<?php

namespace AppBundle\Form;

//use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\DateType;//for date
use Symfony\Component\Form\Extension\Core\Type\TextType;//for date

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClientFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name')
                ->add('email', EmailType::class)
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'Активний'  => 'active',
                        'Тимчасово призупинено'  => 'pasive',
                        'В архіві'  => 'archive',
                    ]
                ])
                ->add('type',ChoiceType::class, [
                    'label' => 'Тип співпраці',
                    'choices' => [
                        'Продаж'  => 'sale',
                        'Оренда'  => 'rent',
                        'Комерція продаж'  => 'comercial_sale',
                        'Комерція оренда'  => 'comercial_rent',
//                        'Все'  => 'all',
                    ]
                ])
                ->add('info',TextareaType::class)
                ->add('phones',CollectionType::class,[
                    'label_attr' => ['class'=>'textCollection'],
                    'entry_type' => TextType::class,
                    'allow_delete' => true,
                    'allow_add' => true,
                ])
                ->add('user',EntityType::class,[
                    'placeholder' => 'Виберіть чий клієнт',
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repo) {
                        return $repo->findAllUsers();
                    }
                ])
                ->add('lastUpdate',DateType::class,[
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('created',DateType::class,[
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('owner',ChoiceType::class, [
                    'label' => 'Тип',
                    'choices' => [
                        'Власник' => true,
                        'Клієнт'  => false
                    ]
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Client'
        ]);
    }
}