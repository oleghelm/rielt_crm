<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ClientRepository;
use AppBundle\Repository\LocationRepository;
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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\Type\EntityHiddenType;

class BidFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class, ['label' => 'Назва'])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'Нова'  => 'new',
                        'В роботі'  => 'inwork',
                        'Оформляється покупка'  => 'buyprogres',
                        'Призупинено'  => 'pause',
                        'Успішно завершено'  => 'succesfinish',
                        'Відмінена'  => 'cancel',
                        'В архіві'  => 'archive',
                    ]
                ])
                ->add('type',ChoiceType::class, [
                    'label' => 'Тип',
                    'choices' => [
                        'Продаж' => 'simple_sale',
                        'Оренда'  => 'simple_rent',
                        'Комерція продаж'  => 'comercial_sale',
                        'Комерція оренда'  => 'comercial_rent',
                    ]
                ])
                ->add('important',ChoiceType::class, [
                    'label' => 'Важливий',
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ]
                ])
                ->add('baseprice',ChoiceType::class, [
                    'label' => 'Основна ціна',
                    'choices' => [
                        'Ціна в $' => 'price',
                        'Ціна в $ за м2' => 'price_m2',
                        'Ціна в грн' => 'price_uah',
                        'Ціна в грн за м2' => 'price_m2_uah',
                    ],
                    'data' => 'price'
                ])
//                ->add('rooms', IntegerType::class, ['label' => 'Кількість кімнат'])
                ->add('rooms', ChoiceType::class, ['multiple' => true,'label' => 'Кількість кімнат','choices'=>[1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10]])
                ->add('min_price', IntegerType::class, ['label' => 'Мінімальна ціна'])
                ->add('max_price', IntegerType::class, ['label' => 'Максимальна ціна'])
                ->add('info', TextareaType::class, ['label' => 'Опис'])
                ->add('user',EntityType::class,[
                    'label' => 'Хто веде',
                    'placeholder' => "Виберіть хто веде об'єкт",
                    'class' => User::class,
                    'query_builder' => function(UserRepository $repo) {
                        return $repo->findAllUsers();
                    }
                ])
                ->add('client',EntityHiddenType::class,[
                    'label' => 'Власник',
                    'attr' => ['class'=>'entity_autocomplete client_id','data-href'=>'/crm/clients/search_ajax','data-hidden-input-text'=>'.client_autocomplete','data-input-label-text'=>'Введіть клієнта'],
                    'class' => Client::class,
                ])
//                ->add('client',EntityHiddenType::class,[
//                    'label' => 'Власник',
//                    'attr' => ['class'=>'entity_autocomplete client_id','data-href'=>'/crm/clients/search_ajax','data-hidden-input-text'=>'.client_autocomplete','data-input-label-text'=>'Введіть клієнта'],
//                    'class' => Client::class,
//                ])
//                ->add('client',EntityType::class,[
//                    'label' => 'Хто подав заявку',
//                    'placeholder' => "Виберіть хто подав заявку",
//                    'class' => Client::class,
//                    'query_builder' => function(ClientRepository $repo) {
//                        return $repo->findAllClients();
//                    }
//                ])
                ->add('lastUpdate',DateType::class,[
                    'label' => 'Останній контакт',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ]);
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Bid'
        ]);
    }
}