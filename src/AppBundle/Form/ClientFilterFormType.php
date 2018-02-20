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

class ClientFilterFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class,[
                    'label' => "Прізвище, ім'я"
                ])
                ->add('phones',TextType::class,[
                    'label' => 'Телефон'
                ])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'placeholder' => 'Виберіть статус',
                    'choices' => [
                        'Активний'  => 'active',
                        'Тимчасово призупинено'  => 'pasive',
                        'В архіві'  => 'archive',
                    ]
                ])
                ->add('user',EntityType::class,[
                    'placeholder' => 'Виберіть чий клієнт',
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repo) {
                        return $repo->findAllUsers();
                    },
//                    'multiple' => true
                ])
//                ->add('lastUpdate',DateType::class,[
//                    'label' => 'Останній контакт раніше',
//                    'widget' => 'single_text',
//                    'html5' => false,
//                    'attr' => ['class' => 'js-datepicker']
//                ])
                ->add('owner',ChoiceType::class, [
                    'placeholder' => 'Виберіть тип',
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
//            'data_class' => 'AppBundle\Entity\Client'
        ]);
    }
}