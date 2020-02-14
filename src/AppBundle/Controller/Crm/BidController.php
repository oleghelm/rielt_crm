<?php


namespace AppBundle\Controller\Crm;

use AppBundle\Entity\User;
use AppBundle\Entity\Bid;
use AppBundle\Entity\BidParam;
use AppBundle\Entity\Location;
use AppBundle\Entity\Client;
use AppBundle\Form\BidFormType;
use AppBundle\Form\ClientShortFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Service\FileUploader;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use AppBundle\Repository\ClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class BidController extends Controller {
    /**
     * @Route("/bids", name="crm_bid_list")
     */
    public function indexAction(Request $request)
    {
        //render params form
        $paramsForm = $this->getParamsFilterForm();
        $paramsForm->handleRequest($request);
        
        $filterData = $paramsForm->getData();
        if($filterData)
            $filter = $this->prepareFilterParams($filterData);
        else 
            $filter = $this->prepareFilterParams([]);
        
        $query = $this->getDoctrine()->getRepository('AppBundle:Bid')->getFilteredBids($filter);
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );
        
        return $this->render('crm/bid/list.html.twig', array(
            'bids' => $result,
            'paramsForm' => $paramsForm->createView(),
        ));
    }
        
    /**
     * @Route("/bids/user/{id}/checklastactive", name="crm_bid_checklastactive_list")
     */
    public function checkLastActiveAction(User $user)
    {
        $callBids = $this->getDoctrine()->getRepository('AppBundle:Bid')->getBidsForCall($user);
        return $this->render('crm/bid/_list_lastActive_tickets.html.twig',['items'=>$callBids]);
    }
    
    /**
     * @Route("/bids/new", name="crm_bid_new")
     */
    public function newAction(Request $request, FileUploader $fileUploader){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $bid = new Bid();
        $bid->setUser($user);
        if($request->get('client_id',false)){
            $client = $this->getDoctrine()->getRepository('AppBundle:Client')->find($request->get('client_id'));
            $bid->setClient($client);
        }
        $form = $this->createForm(BidFormType::class,$bid);
        $form->add('location',ChoiceType::class, [
                    'label' => 'Райони',
                    'multiple' => true,
                    'choices' => $this->getDoctrine()->getRepository('AppBundle:Location')->getLocationsForFilter()
                ]);

        //render params form
        $paramsForm = $this->getParamsForm(null);
        $paramsForm->handleRequest($request);
        
        $clientForm = $this->createForm(ClientShortFormType::class);
        $clientForm->handleRequest($request);
        
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bid = $form->getData();
            
            if($request->get('new_client','N')=='Y'){
                $client = $clientForm->getData();
                if($bid->getUser())
                    $client->setUser($bid->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
                $bid->setClient($client);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($bid);
            $em->flush();
            
            //save params
            $paramsForm = $this->getParamsForm($bid);
            $paramsForm->handleRequest($request);
            $this->saveFormParams($bid,$paramsForm->getData());
            
            $this->addFlash('success', 'Bid created!');

            if($request->get('submitType')!='apply')
                return $this->redirectToRoute('crm_bid_list');
            else
                return $this->redirectToRoute('crm_bid_edit',['id'=>$bid->getId()]);
        }

        return $this->render('crm/bid/new.html.twig', [
            'form' => $form->createView(),
            'paramsForm' => $paramsForm->createView(),
            'clientForm' => $clientForm->createView(),
            'bid' => $bid
        ]);
    }
    
    /**
     * @Route("/bids/{id}/edit", name="crm_bid_edit")
     */
    public function editAction(Request $request, Bid $bid, FileUploader $fileUploader){
//        if(file_exists($this->getParameter('user_photo_directory').'/'.$bid->getPhotos()) && is_file($this->getParameter('user_photo_directory').'/'.$bid->getPhotos()))

        $form = $this->createForm(BidFormType::class, $bid);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form->add('location',ChoiceType::class, [
                    'label' => 'Райони',
                    'multiple' => true,
                    'choices' => $this->getDoctrine()->getRepository('AppBundle:Location')->getLocationsForFilter()
                ]);
        
        $form->handleRequest($request);
        
        $clientForm = $this->createForm(ClientShortFormType::class);
        $clientForm->handleRequest($request);
        
        //render params form
        $paramsForm = $this->getParamsForm($bid);
        $paramsForm->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bid = $form->getData();
            
            //attach new client
            if($request->get('new_client','N')=='Y'){
//                dump($request->get('new_client','N'));die;
                $client = $clientForm->getData();
                if($bid->getUser())
                    $client->setUser($bid->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
                $bid->setClient($client);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($bid);
            $em->flush();
            
            //save params
            $this->saveFormParams($bid,$paramsForm->getData());

            $this->addFlash('success', 'Bid updated!');

            if($request->get('submitType')!='apply')
                return $this->redirectToRoute('crm_bid_list');
            else
                return $this->redirectToRoute('crm_bid_edit',['id'=>$bid->getId()]);
        }

        
        
        return $this->render('crm/bid/edit.html.twig', [
            'form' => $form->createView(),
            'paramsForm' => $paramsForm->createView(),
            'clientForm' => $clientForm->createView(),
            'bid' => $bid
        ]);
    }
        
    /**
     * @Route("/bids/{id}/delete", name="crm_bid_delete")
     */
    public function deleteAction(Request $request, Bid $bid){
        //check permissions if SuperUser Or Owner
        if (!$bid) {
            throw $this->createNotFoundException('No bid found');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($bid);
        $em->flush();

        $this->addFlash('success', 'Bid deleted!');
        
        return $this->redirectToRoute('crm_bid_list');
    }
       
    /**
     * @Route("/bids/{id}/changestatus", name="crm_bid_changestatus")
     */
    public function changeStatusAction(Request $request, Bid $bid){
        //check permissions if SuperUser Or Owner
        if (!$bid) {
            throw $this->createNotFoundException('No bid found');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $bid->setStatus($request->get('status','new'));
        $em->persist($bid);
        $em->flush();

        $this->addFlash('success', 'Статус змінено');
        
        return $this->redirectToRoute('crm_bid_list');
    }
 
    /**
     * @Route("/bids/{id}/search_object", name="crm_bid_search_object")
     */
    public function searchObjectAction(Request $request, Bid $bid){
        //check permissions if SuperUser Or Owner
        if (!$bid) {
            throw $this->createNotFoundException('No bid found');
        }
        
        $queryString = [];
        
        $queryString['type'] = $bid->getType();
        switch ($bid->getBaseprice()){
            case 'price_m2': 
                    $queryString['price_type'] = 'm2';
                    $queryString['currency'] = 'dol';
                break;
            case 'price_uah': 
                    $queryString['price_type'] = 'full';
                    $queryString['currency'] = 'uah';
                break;
            case 'price_m2_uah': 
                    $queryString['price_type'] = 'm2';
                    $queryString['currency'] = 'uah';
                break;
            default: 
                    $queryString['price_type'] = 'full';
                    $queryString['currency'] = 'dol';
                break;
        }
        $queryString['min_price'] = $bid->getMinPrice();
        $queryString['max_price'] = $bid->getMaxPrice();
        $queryString['location'] = $bid->getLocation();
        $queryString['rooms'] = $bid->getRooms();
        $params = $bid->getParamsArrayMap();
//dump($params);die;
        foreach($params as $param){
            if($param['param_id']==23)continue;
            switch($param['type']){
                case 'text': 
                        $queryString[$param['param_id']] = $param['val'];
                    break;
                case 'float':
                case 'integer':
                case 'diapazon':
                case 'floatdiapazon':
                    if(!isset($queryString['min_'.$param['param_id']]))
                        $queryString['min_'.$param['param_id']] = $param['val'];
                    elseif($queryString['min_'.$param['param_id']] > $param['val'])
                        $queryString['min_'.$param['param_id']] = $param['val'];
                        
                    if(!isset($queryString['max_'.$param['param_id']]))
                        $queryString['max_'.$param['param_id']] = $param['val'];
                    elseif($queryString['max_'.$param['param_id']] < $param['val'])
                        $queryString['max_'.$param['param_id']] = $param['val'];
                    break;
                case 'select': 
                    $queryString[$param['param_id']][] = $param['val'];
                    break;
            }
        }

        $this->addFlash('success', 'Фльтр заповнено згідно заявки №'.$bid->getId());
        
        return $this->redirectToRoute('crm_object_list',['bid'=>$bid->getId(),'form'=>$queryString]);
    }
    
    /**
     * @Route("/bids/{id}", name="crm_bid_show")
     */
    public function showAction(Request $request, Bid $bid){
        
        if (!$bid) {
            throw $this->createNotFoundException('No bid found');
        }
        
        //prepare params array
        $params = [];
        $bidParams = $bid->getParams();
        foreach ($bidParams as $bidParam){
            $param = $bidParam->getParam();
            $multiple = $param->getMultiple();
            switch ($param->getType()){
                case 'diapazon':
                case 'integer': $val = $bidParam->getNumber(); break;
                case 'floatdiapazon': 
                case 'float': $val = $bidParam->getFloatnumber(); break;
                case 'text': $val = $bidParam->getString(); break;
                case 'select': $val = $bidParam->getProperty()->getName(); $multiple=true; break;
            }
            if($multiple){
                $params[$param->getId()]['val'][] = $val;
            } else {
                $params[$param->getId()]['val'] = $val;
            }
            $params[$param->getId()]['name'] = $param->getName();
            $params[$param->getId()]['multiple'] = $multiple;
        }
        
        if($bid->getLocation()){
            $locations = $this->getDoctrine()->getRepository('AppBundle:Location')->findBy(['id'=>$bid->getLocation()]);
            $params['locations'] = [
                'name' => "Райони",
                'multiple' => true,
                'val' => $locations
            ];
        }
        
        $favourites = $bid->getFavourites();
        if($favourites){
            $objects = [];
            foreach($favourites as $favourite){
                $objects[] = $favourite->getObject();
            }
            $bid->setObjects($objects);
        }
        
        if($request->get('ajax','')=='Y'){
            $tmpl = 'crm/bid/_show.html.twig';
        } else {
            $tmpl = 'crm/bid/show.html.twig';
        }
        
        return $this->render($tmpl, array(
            'bid' => $bid,
            'params' => $params
        ));
    }
    
    public function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
    
    /** 
     * remake ready params from filter form for repository filtering objects
     * delete empty params and change types for filter variant */
    public function prepareFilterParams($filterParams = []){
        $params = [];
        //remove empty vals
        foreach($filterParams as $key=>$val){if($val){$params[$key]['val'] = $val;}}
        if(isset($this->listFilter['type'])){
            $params['type']['val'] = $this->listFilter['type'];
        }
        $formParams = $this->getParamsFilterFormParams();
        foreach($formParams as $formParam){
            if(isset($params[$formParam['id']])){
                $params[$formParam['id']]['type'] = $formParam['type'];
            }
            if(isset($params['min_'.$formParam['id']])){
                $params[$formParam['id']]['type'] = 'diapazon';
                $params[$formParam['id']]['val']['min'] = $params['min_'.$formParam['id']]['val'];
            } 
            if(isset($params['max_'.$formParam['id']])){
                $params[$formParam['id']]['type'] = 'diapazon';
                $params[$formParam['id']]['val']['max'] = $params['max_'.$formParam['id']]['val'];
            }
        }
        return $params;
    }
    /**
     * find all created params (total params library)
     * @return array
     */
    public function getParamsForForm($forFilter = false){
        $em = $this->getDoctrine()->getManager();
        if($forFilter)
            $params = $em->getRepository('AppBundle:Param')->findAllParamsForBidFilterForm();
        else
            $params = $em->getRepository('AppBundle:Param')->findAllParamsForBidForm();
        $formParams = [];
        foreach($params as $param){
            $formParam = [
                'id' => $param->getId(),
                'type' => $param->getType(),
                'label' => $param->getName(),
                'multiple' => $param->getMultiple(),
            ];
            if(in_array($formParam['type'],['select'])){
                $choices = [];
                foreach($param->getProperties() as $prop){
                    $choices[$prop->getName()] = $prop->getId();
                }
                $formParam['choices'] = $choices;
                $formParam['multiple'] = true;
            }
            if($formParam['type']=='integer'){
                $formParam['multiple']=true;
                $formParam['type']='diapazon';
            }
            if($formParam['type']=='float'){
                $formParam['multiple']=true;
                $formParam['type']='floatdiapazon';
            }
            $formParams[] = $formParam;
        }
        return $formParams;
    }
    
    public function prepareFormParams($formParams,$bidformParams){
        foreach ($formParams as $formParam){
            switch($formParam['type']){
                case 'text': 
                    break;
                case 'float': 
                case 'integer': 
                    break;
                case 'diapazon':
                case 'floatdiapazon': 
                    if(isset($bidformParams[$formParam['id']])){
                        if(!isset($bidformParams[$formParam['id']][1])){
                            $bidformParams['min_'.$formParam['id']] = 0;
                            $bidformParams['max_'.$formParam['id']] = $bidformParams[$formParam['id']][0];
                        } else {
                            $bidformParams['min_'.$formParam['id']] = $bidformParams[$formParam['id']][0];
                            $bidformParams['max_'.$formParam['id']] = $bidformParams[$formParam['id']][1];
                        }
                    }
                    break;
                case 'select': 
                    break;
            }
        }
        return $bidformParams;
    }
    /**
     * get params form for edit page
     */
    public function getParamsForm(Bid $bid = null){
        //get params and Properties for build
        $formParams = $this->getParamsForForm();
        if($bid){
            $bidformParams = $bid->getParamsArrayMap(true);
            $bidformParams = $this->prepareFormParams($formParams, $bidformParams);
        } else {
            $bidformParams = [];
        }
        return $this->generateParamsForm($formParams, ['data'=>$bidformParams]);
    }
    
    /**
     * get params for filter form and for repository
     * remake ready params for filter form 
     */
    public function getParamsFilterFormParams(){
        $defParams = $this->addDefaultBidSearchFields();
        $formParams = $this->getParamsForForm(true);
        
        foreach($formParams as &$formParam){
            if($formParam['type']=='text'){
                $formParam['multiple']=false;
            }
            if($formParam['type']=='select'){
                $formParam['multiple']=true;
            }
            if($formParam['type']=='integer'){
                $formParam['type']='diapazon';
                $formParam['multiple']=false;
            }
            if($formParam['type']=='float'){
                $formParam['type']='floatdiapazon';
                $formParam['multiple']=false;
            }
        }
        $formParams = array_merge($defParams,$formParams);
        return $formParams;
    }
    /**
     * get filter form fot list of elements
     */
    public function getParamsFilterForm(){
        $formParams = $this->getParamsFilterFormParams();
//        dump($formParams);
        return $this->generateParamsForm($formParams,['method'=>'GET']);
    }
    
    public function addDefaultBidSearchFields($formParams = []){
        
        $formParams[] = [
            'id' => 'status',
            'type' => 'select',
            'label' => 'Статус',
            'multiple' => false,
            'choices' => [
                'Всі'  => 'all',
                'Нова'  => 'new',
                'В роботі'  => 'inwork',
                'Оформляється покупка'  => 'buyprogres',
                'Призупинено'  => 'pause',
                'Успішно завершено'  => 'succesfinish',
                'Відмінена'  => 'cancel',
                'В архіві'  => 'archive',
            ]
        ];
        $formParams[] = [
            'id' => 'type',
            'type' => 'select',
            'label' => 'Тип',
            'multiple' => false,
            'choices' => [
                'Продаж' => 'simple_sale',
                'Оренда'  => 'simple_rent',
                'Комерція продаж'  => 'comercial_sale',
                'Комерція оренда'  => 'comercial_rent',
                ],
        ];
        $formParams[] = [
            'id' => 'user',
            'type' => 'select',
            'label' => 'Ріелтор',
            'multiple' => false,
            'choices' =>  $this->getDoctrine()->getRepository('AppBundle:User')->getUsersForFilter(),
        ];
        $formParams[] = [
            'id' => 'company',
            'type' => 'checkbox',
            'label' => 'Компанія',
            'multiple' => true,
            'choices' =>  $this->getDoctrine()->getRepository('AppBundle:Company')->getForFilter(),
            'choice_attr' =>  $this->getDoctrine()->getRepository('AppBundle:Company')->getImagesForFilter(),
        ];
        $formParams[] = [
            'id' => 'location',
            'type' => 'select',
            'label' => 'Район',
            'multiple' => true,
            'choices' =>  $this->getDoctrine()->getRepository('AppBundle:Location')->getLocationsForFilter(),
        ];
        $formParams[] = [
            'id' => 'rooms',
            'type' => 'select',
            'label' => 'К-сть кімнат',
            'multiple' => true,
            'choices' =>  [1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10],
        ];
        $formParams[] = [
            'id' => 'min_price',
            'type' => 'integer',
            'label' => 'Ціна від',
            'multiple' => false,
        ];
        $formParams[] = [
            'id' => 'max_price',
            'type' => 'integer',
            'label' => 'Ціна до',
            'multiple' => false,
        ];
        
        return $formParams;
    }
    
    public function generateParamsForm($formParams, $defaultParams = []){
        $formBuilder = $this->createFormBuilder(null,$defaultParams);
        foreach($formParams as $formParam){
            switch ($formParam['type']){
                case 'select':
                    $formBuilder->add($formParam['id'],ChoiceType::class, [
                        'placeholder' => '',
                        'label' => $formParam['label'],
                        'choices' => $formParam['choices'],
                        'multiple' => $formParam['multiple']
                    ]);
                    break;
                case 'checkbox':
                    $formBuilder->add($formParam['id'],ChoiceType::class, [
                        'label' => $formParam['label'],
                        'choices' => $formParam['choices'],
                        'choice_attr' => $formParam['choice_attr'],
                        'multiple' => $formParam['multiple'],
                        'expanded' => true,
                    ]);
                    break;
                case 'text':
                    if($formParam['multiple']){
                        $formBuilder->add($formParam['id'],CollectionType::class,[
                            'label' => $formParam['label'],
                            'label_attr' => ['class'=>'textCollection'],
                            'entry_type' => TextType::class,
                            'allow_delete' => true,
                            'allow_add' => true,
//                            'data_class'=>'multi'
                        ]);
                    } else {
                        $formBuilder->add($formParam['id'],TextType::class, [
                            'label' => $formParam['label']
                        ]);
                    }
                    break;
                case 'integer':
                    if($formParam['multiple']){
                        $formBuilder->add($formParam['id'],CollectionType::class,[
                            'label' => $formParam['label'],
                            'label_attr' => ['class'=>'textCollection'],
                            'entry_type' => IntegerType::class,
                            'allow_delete' => true,
                            'allow_add' => true,
//                            'data_class'=>'multi'
                        ]);
                    } else {
                        $formBuilder->add($formParam['id'],IntegerType::class, [
                            'label' => $formParam['label']
                        ]);
                    }
                    break;
                case 'float':
                    if($formParam['multiple']){
                        $formBuilder->add($formParam['id'],CollectionType::class,[
                            'label' => $formParam['label'],
                            'label_attr' => ['class'=>'textCollection'],
                            'entry_type' => TextType::class,
                            'allow_delete' => true,
                            'allow_add' => true,
                        ]);
                    } else {
                        $formBuilder->add($formParam['id'],TextType::class, [
                            'label' => $formParam['label']
                        ]);
                    }
                    break;
                case 'diapazon':
                        $formBuilder->add('min_'.$formParam['id'],IntegerType::class, [
                            'label' => $formParam['label'].' від'
                        ]);
                        $formBuilder->add('max_'.$formParam['id'],IntegerType::class, [
                            'label' => $formParam['label'].' до'
                        ]);
                    break;
                case 'floatdiapazon':
                        $formBuilder->add('min_'.$formParam['id'],TextType::class, [
                            'label' => $formParam['label'].' від'
                        ]);
                        $formBuilder->add('max_'.$formParam['id'],TextType::class, [
                            'label' => $formParam['label'].' до'
                        ]);
                    break;
                default:
                    break;
            }
        }
        return $formBuilder->getForm();
    }
    
    /**
     * for saving
     */
    public function compareOldAndNewParams($oldParams,$newParams){
        $result = ['update'=>[],'delete'=>[],'insert'=>[]];
        //find diapazone hack
        foreach ($newParams as $k=>$newParam){
            if(strpos($k,'_')){
                $tmp = explode('_',$k);
                if(is_array($newParams) && !isset($newParams[$tmp[1]])){$newParams[$tmp[1]] = [];}
                unset($newParams[$tmp[1]][0]);
                unset($newParams[$tmp[1]][1]);
                $newParams[$tmp[1]][$tmp[0]] = $newParam;
                unset($newParams[$k]);
            }
        }
        //find params, what not isset in new params
        foreach($oldParams as $oldParam){
            if(isset($newParams[$oldParam['param_id']])){
                if(!is_array($newParams[$oldParam['param_id']]) && $newParams[$oldParam['param_id']]==$oldParam['val']){//if new value not array and old value == new value
                    unset($newParams[$oldParam['param_id']]);//we will not do any thing with it
                    unset($oldParams[$oldParam['id']]);//this value ok. don`t touch it
                    continue;
                } elseif (is_array($newParams[$oldParam['param_id']]) && in_array($oldParam['val'], $newParams[$oldParam['param_id']])){ // if new value is array and old value in it
                    unset($newParams[$oldParam['param_id']][array_search($oldParam['val'], $newParams[$oldParam['param_id']])]);//we will not do any thing with it
                    unset($oldParams[$oldParam['id']]);//this value ok. don`t touch it
                    continue;
                } elseif(is_array($newParams[$oldParam['param_id']]) && !in_array($oldParam['val'], $newParams[$oldParam['param_id']])){//if new value is array and old value not in it
                    $result['delete'][] = $oldParam['id'];
                    unset($oldParams[$oldParam['id']]);//value checked
                    continue;
                } elseif(!is_array($newParams[$oldParam['param_id']]) && $newParams[$oldParam['param_id']]!=$oldParam['val']){ //if new value not an array and old value != to it
                    $result['update'][] = [
                        'id' => $oldParam['id'],
                        'type' => $oldParam['type'],
                        'val' => $newParams[$oldParam['param_id']]
                    ];
                    unset($newParams[$oldParam['param_id']]);//value checked and wil be update
                    unset($oldParams[$oldParam['id']]);//value checked
                    continue;
                }
            } else {
                $result['delete'][] = $oldParam['id'];
                unset($oldParams[$oldParam['id']]);//value checked
                continue;
            }
        }
        //add other new params
        foreach($newParams as $param_id=>$newParam){
            if(!empty($newParam))
                if(!is_array($newParam))
                    $result['insert'][] = [
                        'param_id' => $param_id,
                        'val' => $newParam
                    ];
                else
                    foreach($newParam as $p)
                        $result['insert'][] = [
                            'param_id' => $param_id,
                            'val' => $p
                        ];
        }
                
        return $result;
    }
    
    public function saveFormParams(Bid $bid, $newbidParams){
        $currentbidParams = $bid->getParamsArrayMap();
        $comparedParams = $this->compareOldAndNewParams($currentbidParams,$newbidParams);
        
        $formParams_ = $this->getParamsForForm();$formParams = [];
        //resort
        foreach($formParams_ as $fp){$formParams[$fp['id']] = $fp;}
        
        $em = $this->getDoctrine()->getEntityManager();
        if(!empty($comparedParams['delete'])){
            foreach($comparedParams['delete'] as $forDelete){
                $bp = $this->getDoctrine()->getRepository('AppBundle:BidParam')->find($forDelete);
                $em->remove($bp);
                $em->flush();
            }
        }
        
        if(!empty($comparedParams['update'])){
            foreach($comparedParams['update'] as $forUpdate){
                $bp = $this->getDoctrine()->getRepository('AppBundle:BidParam')->find($forUpdate['id']);
                switch($forUpdate['type']){
                    case 'text': 
                        $bp->setString($forUpdate['val']);
                        break;
                    case 'integer': 
                        $bp->setNumber($forUpdate['val']);
                        break;
                    case 'float': 
                        $bp->setFloatnumber($forUpdate['val']);
                        break;
                    case 'select': 
                        $property = $this->getDoctrine()->getRepository('AppBundle:Property')->find($forUpdate['val']);
                        $bp->setProperty($property);
                        break;
                }
                $em->persist($bp);
                $em->flush();
            }
        }
        
        if(!empty($comparedParams['insert'])){
            foreach($comparedParams['insert'] as $forInsert){
                $bp = new BidParam();
                $bp->setBid($bid);
                $param = $this->getDoctrine()->getRepository('AppBundle:Param')->find($forInsert['param_id']);
                $bp->setParam($param);
                switch($formParams[$forInsert['param_id']]['type']){
                    case 'text': 
                        $bp->setString($forInsert['val']);
                        break;
                    case 'integer': 
                    case 'diapazon': 
                        $bp->setNumber($forInsert['val']);
                        break;
                    case 'floatdiapazon': 
                    case 'float': 
                        $bp->setFloatnumber(floatval($forInsert['val']));
                        break;
                    case 'select': 
                        $property = $this->getDoctrine()->getRepository('AppBundle:Property')->find($forInsert['val']);
                        $bp->setProperty($property);
                        break;
                }
                $em->persist($bp);
                $em->flush();
            }
        }        
    }
}