<?php

namespace AppBundle\Form;

use AppBundle\Entity\Property;
use AppBundle\Entity\Param;
use AppBundle\Repository\ParamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\HiddenType;//for chices
use Symfony\Component\Form\Extension\Core\Type\TextType;//for chices


class PropertyFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class,['label'=>'Значення'])
                ->add('exportName',TextType::class,['label'=>'Значення в вигрузці'])
                ->add('sort',TextType::class,['label'=>'Порядок'])
                ->add('param',EntityType::class,[
                    'label' => 'Параметр',
                    'class' => Param::class,
                    'query_builder' => function(ParamRepository $repo) {
                        $qb = $repo->findAllParamsForProperties();
                        if(isset($_REQUEST['param_id']) && $_REQUEST['param_id']!=""){
                            $qb->andWhere('param.id = :id')->setParameter('id', $_REQUEST['param_id']);
                        }
                        return $qb;
                    },
                    'data'=>(isset($_REQUEST['param_id']) && $_REQUEST['param_id']!="") ? $_REQUEST['param_id'] : null
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Property'
        ]);
    }
}