<?php

namespace AppBundle\Form;

//use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Entity\Client;
use AppBundle\Entity\Object;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ClientRepository;
use AppBundle\Repository\ObjectRepository;
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

class AgreementFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name', TextType::class,['label' => 'Назва'])
                ->add('price', TextType::class,['label' => 'Ціна'])
                ->add('comision', TextType::class,['label' => 'Комісія'])
                ->add('total', TextType::class,['label' => 'Всього'])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'Активний'  => 'active',
                        'В архіві'  => 'archive',
                    ]
                ])
                ->add('flags',ChoiceType::class, [
                    'label' => 'Відмітки',
                    'multiple' => true,
//                    'expanded' => true,
                    'choices' => [
                        'Ексклюзив' => 'exclusive',
                        'Продаж' => 'simple_sale',
                        'Оренда' => 'simple_rent',
                        'Ком. продаж' => 'comercial_sale',
                        'Ком. оренда' => 'comercial_rent',
                    ]
                ])
                ->add('info',TextareaType::class,['label' => 'Опис'])
                ->add('user',EntityType::class,[
                    'label' => 'Ріелтор',
                    'placeholder' => '',
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repo) {
                        return $repo->findAllUsers();
                    }
                ])
                ->add('client',EntityType::class,[
                    'label' => 'Клієнт',
                    'placeholder' => '',
                    'class' => Client::class,
                    'query_builder' => function(ClientRepository $repo) {
                        return $repo->findAllClients();
                    }
                ])
                ->add('object',EntityType::class,[
                    'placeholder' => "Об'єкт",
                    'class' => Object::class,
                    'query_builder' => function(ObjectRepository $repo) {
                        return $repo->findAllObjects();
                    }
                ])
                ->add('date',DateType::class,[
                    'label' => 'Дата',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Agreement'
        ]);
    }
}