<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TicketMyFilterFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('date_current',DateType::class,[
                    'label' => 'Дата',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('date_from',DateType::class,[
                    'label' => 'Дата від',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('date_to',DateType::class,[
                    'label' => 'Дата до',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('date_type',ChoiceType::class, [
                    'label' => 'Тип подачі',
                    'choices' => [
                        'По даті' => 'day',
                        'За період' => 'period',
                    ],
                    'data' => 'day'
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
//            'data_class' => 'AppBundle\Entity\Ticket'
        ]);
    }
}