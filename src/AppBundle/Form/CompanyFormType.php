<?php

namespace AppBundle\Form;

use AppBundle\Entity\Company;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;//for date

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CompanyFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name', TextType::class,['label'=>'Назва'])
                ->add('email', EmailType::class,['label'=>'Пошта'])
                ->add('logo',FileType::class, ['data_class' => null,'label'=>'Логотип'])
                ->add('preview',FileType::class, ['data_class' => null,'label'=>"Прев'ю"])
                ->add('info',TextareaType::class,['label'=>'Інформаця'])
                ->add('phones',CollectionType::class,[
                    'label'=>'Телефони',
                    'label_attr' => ['class'=>'textCollection'],
                    'entry_type' => TextType::class,
                    'allow_delete' => true,
                    'allow_add' => true,
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Company'
        ]);
    }
}