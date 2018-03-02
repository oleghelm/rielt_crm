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

class TicketFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('date',DateType::class,[
                    'label' => 'Дата',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('time',TextType::class,[
                    'label' => 'Час',
                    'attr' => ['class' => 'js-timepicker']
                ])
//                ->add('created',DateType::class,[
//                    'label' => 'Створено',
//                    'widget' => 'single_text',
//                    'html5' => false,
//                    'attr' => ['class' => 'js-datepicker']
//                ])
                ->add('place',TextareaType::class, ['label' => 'Місце проведення'])
                ->add('clientName',TextType::class, ['label' => "Або введіть ім'я"])
                ->add('task',ChoiceType::class, [
                    'label' => 'Ціль',
                    'choices' => [
                        'Зустріч'  => 'meet',
                        "Показ об'єкту"  => 'show',
                        "Оформлення нового об'єкту"  => 'creating',
                        'Оформлення документів'  => 'docs',
                        'Збори в офісі'  => 'standup',
                    ]
                ])
                ->add('result',ChoiceType::class, [
                    'label' => 'Результат',
                    'choices' => [
                        'Успішно'  => 'success',
                        "Не успішно"  => 'notsuccess',
                    ]
                ])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'Новий'  => 'new',
                        'В роботі'  => 'inwork',
                        'Перенесено'  => 'replace',
                        'Виконано'  => 'done',
                        'Відмінено'  => 'cancel',
                    ]
                ])
                ->add('info',TextType::class, ['label' => 'Інформація'])

                ->add('user',EntityType::class,[
                    'label' => 'Працівник',
                    'placeholder' => "",
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repo) {
                        return $repo->findAllUsers();
                    }
                ])
                ->add('client',EntityType::class,[
                    'label' => 'Виберіть клієнта',
                    'placeholder' => "",
                    'class' => Client::class,
                    'query_builder' => function(ClientRepository $repo) {
                        return $repo->findAllClients();
                    }
                ])
                ->add('object',EntityType::class,[
                    'label' => "Виберіть об'єкт",
                    'placeholder' => "",
                    'class' => Object::class,
                    'query_builder' => function(ObjectRepository $repo) {
                        return $repo->findAllObjects();
                    }
                ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Ticket'
        ]);
    }
}