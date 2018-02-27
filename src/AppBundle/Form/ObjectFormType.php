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

class ObjectFormType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
//        dump($options['data']->getId());
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
                ->add('realty',ChoiceType::class, [
                    'label' => 'Вид нерухомості',
                    'choices' => [
                        'квартира' => 'квартира',
                        'комната' => 'комната',
                        'дім' => 'дом',
                        'частина будинку' => 'часть дома',
                        'дача' => 'дача',
                        'офісне приміщення' => 'офисное помещение',
                        'офісна будівля' => 'офисное здание',
                        'торгові майданчики' => 'торговые площади',
                        'складське приміщення' => 'складские помещения',
                        'виробниче приміщення' => 'производственные помещения',
                        'кафе, бар, ресторан' => 'кафе, бар, ресторан',
                        "об'єкст сфери послуг" => 'объект сферы услуг',
                        'готель' => 'отель, гостиница',
                        'база відпочинку, пансіонат' => 'база отдыха, пансионат',
                        'приміщення вільного призначення' => 'помещения свободного назначения',
                        'готовий бізнес' => 'готовый бизнес',
                        'земля під житлову забудову' => 'участок под жилую застройку',
                        'земля комерційного призначення' => 'земля коммерческого назначения',
                        'земля с/г призначення' => 'земля сельскохозяйственного назначения',
                        'земля рекреаційного призначення' => 'земля рекреационного назначения',
                        'земля природно-заповідного призначення' => 'земля природно-заповедного назначения',
                        'бокс в гаражному комплексі' => 'бокс в гаражном комплексе',
                        'підземный паркінг' => 'подземный паркинг',
                        'місце в гаражному кооперативі' => 'место в гаражном кооперативе',
                        'окремий гараж' => 'отдельно стоящий гараж',
                        'місце на стоянці' => 'место на стоянке',
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
                    'data' => 1
                ])
                ->add('important',ChoiceType::class, [
                    'label' => 'Важливий',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                    'data' => 0
                ])
                ->add('advertising',ChoiceType::class, [
                    'label' => 'Рекламується',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                    'data' => 0
                ])
                ->add('exclusive',ChoiceType::class, [
                    'label' => 'Ексклюзив',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                    'data' => 0
                ])
                ->add('domria',ChoiceType::class, [
                    'label' => 'Вигрузка на dom.ria',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Так' => true,
                        'Ні'  => false
                    ],
                    'data' => 0
                ])
                ->add('comission',ChoiceType::class, [
                    'label' => 'Комісію платить',
                    'expanded' => true,
                    'attr' => array('class' => 'inline-radio'),
                    'choices' => [
                        'Власник' => true,
                        'Клієнт'  => false
                    ],
                    'data' => 0
                ])
                ->add('price', IntegerType::class, ['label' => 'Ціна в $'])
                ->add('price_uah', IntegerType::class, ['label' => 'Ціна в грн'])
                ->add('price_m2', IntegerType::class, ['label' => 'Ціна $ за м2'])
                ->add('price_m2_uah', IntegerType::class, ['label' => 'Ціна в грн за 1 м2'])
                ->add('rooms', IntegerType::class, ['label' => 'Кількість кімнат'])
                ->add('area', TextType::class, ['label' => 'Площа'])
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
                ->add('client',EntityType::class,[
                    'label' => 'Власник',
                    'placeholder' => "Виберіть Кому належить об'єкт",
                    'class' => Client::class,
                    'query_builder' => function(ClientRepository $repo) {
                        return $repo->findAllClients();
                    }
                ])
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