<?php

namespace AppBundle\Form;

use AppBundle\Entity\Location;
use AppBundle\Repository\LocationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\TextareaType;//for chices
use Symfony\Component\Form\Extension\Core\Type\TextType;//for chices


class LocationFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name', TextType::class, ['label' => "Назва"])
                ->add('nameru', TextType::class, ['label' => "Назва (рос)"])
                ->add('note',TextareaType::class, ['label' => "Опис"])
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
                ->add('location',EntityType::class,[
                    'label' => 'Розташування',
                    'placeholder' => "Виберіть розташування",
                    'class' => Location::class,
                    'query_builder' => function(LocationRepository $repo) {
                        return $repo->findAllLocations();
                    }
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Location'
        ]);
    }
}