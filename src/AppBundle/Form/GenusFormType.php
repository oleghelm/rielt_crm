<?php

namespace AppBundle\Form;

use AppBundle\Entity\SubFamily;
use AppBundle\Repository\SubFamilyRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\DateType;//for date

class GenusFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name')
                ->add('subFamily',EntityType::class,[
                    'placeholder' => 'Choose a Sub Family',
                    'class' => SubFamily::class,
                    'query_builder' => function(SubFamilyRepository $repo) {
                        return $repo->createAlphabeticalQueryBuilder();
                    }
                ])
                ->add('speciesCount')
                ->add('funFact')
                ->add('isPublished',ChoiceType::class, [
                    'choices' => [
                        'Yes' => true,
                        'No'  => false
                    ]
                ])
                ->add('firstDiscoveredAt',DateType::class,[
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Genus'
        ]);
    }
}