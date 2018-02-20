<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ClientRepository;
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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ObjectFilterFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
//                ->add('name')
//                ->add('user',EntityType::class,[
//                    'placeholder' => 'Хто веде',
//                    'class' => User::class,
//                    'query_builder' => function(UserRepository $repo) {
//                        return $repo->findAllUsers();
//                    },
//                ])
//                ->add('client',EntityType::class,[
//                    'placeholder' => 'Власник',
//                    'class' => Client::class,
//                    'query_builder' => function(ClientRepository $repo) {
//                        return $repo->findAllClients();
//                    },
//                ])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'В продажу'  => 'insale',
                        'Зарезервовано'  => 'reserved',
                        'Продано'  => 'saled',
                        'В архіві'  => 'archive',
                    ]
                ])
                ->add('type',ChoiceType::class, [
                    'label' => 'Тип',
                    'choices' => [
                        'Продаж' => 'sale',
                        'Оренда'  => 'rent',
                        'Продаж комерція' => 'sale_K',
                        'Оренда комерція'  => 'rent_k',
                    ]
                ])
                ->add('price_from', IntegerType::class, ['label' => 'Ціна від'])
                ->add('price_to', IntegerType::class, ['label' => 'Ціна до'])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
//            'data_class' => 'AppBundle\Entity\Client'
        ]);
    }
}