<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Entity\Location;
use AppBundle\Entity\Company;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\ClientRepository;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\CompanyRepository;
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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Form\Type\EntityHiddenType;

class ObjectFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $builder
                ->add('name',TextType::class, ['label' => 'Заголовок'])
                ->add('code',TextType::class, ['label' => 'Код'])
                ->add('status',ChoiceType::class, [
                    'label' => 'Статус',
                    'choices' => [
                        'В продажу'  => 'insale',
                        'Зарезервовано'  => 'reserved',
                        'Продано'  => 'saled',
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
                ->add('saletype',ChoiceType::class, [
                    'label' => 'Тип операції з нерухомістю',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Первинна' => 2,
                        'Вторинна'  => 1
                    ],
                ])
                ->add('important',ChoiceType::class, [
                    'label' => 'Важливий',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                ])
                ->add('advertising',ChoiceType::class, [
                    'label' => 'Рекламується',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                ])
                ->add('exclusive',ChoiceType::class, [
                    'label' => 'Ексклюзив',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                ])
                ->add('domria',ChoiceType::class, [
                    'label' => 'Вигрузка на dom.ria',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                ])
                ->add('comission',ChoiceType::class, [
                    'label' => 'Комісію платить',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Власник' => true,
                        'Клієнт'  => false
                    ],
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
                ->add('price', MoneyType::class, ['label' => 'Ціна в $','currency'=>'USD'])
                ->add('price_uah', MoneyType::class, ['label' => 'Ціна в грн','currency'=>'UAH'])
                ->add('price_m2', MoneyType::class, ['label' => 'Ціна $ за м2','currency'=>'USD'])
                ->add('price_m2_uah', MoneyType::class, ['label' => 'Ціна в грн за 1 м2','currency'=>'UAH'])
                ->add('rooms', IntegerType::class, ['label' => 'Кількість кімнат'])
                ->add('area', HiddenType::class, ['label' => 'Площа'])
                ->add('info', TextareaType::class, ['label' => 'Опис'])
                ->add('officialinfo', TextareaType::class, ['label' => 'Опис для вигрузки'])
                ->add('address',TextareaType::class, ['label' => 'Адреса'])
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
//                ->add('client',EntityType::class,[
//                    'label' => 'Власник',
//                    'placeholder' => "Виберіть Кому належить об'єкт",
//                    'class' => Client::class,
//                    'query_builder' => function(ClientRepository $repo) {
//                        return $repo->findAllClients();
//                    }
//                ])
                ->add('company',EntityType::class,[
                    'label' => 'Компанія',
                    'placeholder' => "Виберіть компанію",
                    'class' => Company::class,
                    'query_builder' => function(CompanyRepository $repo) {
                        return $repo->findAllCompanies();
                    }
                ])
                ->add('lastUpdate',DateType::class,[
                    'label' => 'Останній контакт',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('location',EntityType::class,[
                    'label' => 'Розташування',
                    'placeholder' => "Виберіть розташування",
                    'class' => Location::class,
                    'query_builder' => function(LocationRepository $repo) {
                        return $repo->findAllCityLocations();
                    }
                ])
                ->add('photos',FileType::class, [
                    'label' => 'Фотографії',
                    'data_class' => null,
                    'multiple' => true
                    ]);
    }

    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Object'
        ]);
    }
}