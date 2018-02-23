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


class PropertyFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name')
                ->add('sort');
//        if(isset($_REQUEST['param_id']) && $_REQUEST['param_id']!=""){
//            $builder->add('param',HiddenType::class, ['data'=>$_REQUEST['param_id']]);
//        } else 
            $builder->add('param',EntityType::class,[
                    'class' => Param::class,
                    'query_builder' => function(ParamRepository $repo) {
                        $qb = $repo->findAllParamsForProperties();
                        if(isset($_REQUEST['param_id']) && $_REQUEST['param_id']!=""){
                            $qb->andWhere('param.id = :id')->setParameter('id', $_REQUEST['param_id']);
                        }
                        return $qb;
                    },
                    'data'=>(isset($_REQUEST['param_id']) && $_REQUEST['param_id']!="") ? $_REQUEST['param_id'] : null
                ]);
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Property'
        ]);
    }
}