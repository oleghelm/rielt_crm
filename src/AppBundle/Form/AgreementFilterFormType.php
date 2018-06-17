<?php

namespace AppBundle\Form;

//use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Entity\Client;
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

class AgreementFilterFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class,[
                    'label' => "Назва"
                ])
                ->add('user',EntityType::class,[
                    'label' => 'Ріелтор',
                    'placeholder' => 'Виберіть ріелтора',
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repo) {
                        return $repo->findAllUsers();
                    },
//                    'multiple' => true
                ])
                ->add('client',EntityType::class,[
                    'label' => 'Клієнт',
                    'placeholder' => 'Виберіть клієнта',
                    'class' => Client::class,
                    'query_builder' => function(ClientRepository $repo) {
                        return $repo->findAllClients();
                    },
                    'required' => false,
//                    'multiple' => true
                ])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'Активний'  => 'active',
                        'В архіві'  => 'archive',
                    ]
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
//            'data_class' => 'AppBundle\Entity\Client',
            'validation_groups' => false,
        ]);
    }
}