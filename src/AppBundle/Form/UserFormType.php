<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\DateType;//for date
use Symfony\Component\Form\Extension\Core\Type\TextType;//for date

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name')
                ->add('email', EmailType::class)
                ->add('photo',FileType::class, array('data_class' => null))
//                ->add('photo')
                ->add('info',TextareaType::class)
                ->add('phones',CollectionType::class,[
                    'label_attr' => ['class'=>'textCollection'],
                    'entry_type' => TextType::class,
                    'allow_delete' => true,
                    'allow_add' => true,
                ])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class
                ])
                ->add('roles',ChoiceType::class, [
                    'label' => 'Рівень доступу',
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                        'Доступ в ЦРМ' => 'ROLE_CRM_USER',
                        'Доступ в Адмінпанель' => 'ROLE_ADMIN_PANEL_USER',
                        'Доступ в головний адміністратор'  => 'ROLE_SUPERADMIN'
                    ]
                ])
//                ->add('roles',ChoiceType::class, [
//                    'choices' => [
//                        'Доступ в ЦРМ' => 'ROLE_ADMIN',
//                        'Доступ в адмінпанель'  => 'ROLE_SUPERADMIN'
//                    ]
//                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
    }
}