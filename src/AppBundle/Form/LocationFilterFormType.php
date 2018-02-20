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

class LocationFilterFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class,[
                    'label' => "Назва"
                ])
                ->add('level',ChoiceType::class, [
                    'label' => "Рівень",
                    'multiple' => false,
                    'choices' => [
                        'Країна' => 0,
                        'Область' => 1,
                        'Район області' => 2,
                        'Місто' => 3,
                        'Район міста' => 4,
                        'Вулиця' => 5,
                        'Село' => 6,
                        'Вулиця села' => 7,
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