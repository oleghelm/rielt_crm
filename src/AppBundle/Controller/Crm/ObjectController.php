<?php


namespace AppBundle\Controller\Crm;

use AppBundle\Controller\ParamsFormBuilder;

use AppBundle\Entity\User;
use AppBundle\Entity\Object;
use AppBundle\Entity\ObjectParam;
use AppBundle\Entity\Client;
use AppBundle\Form\ObjectFormType;
use AppBundle\Form\ClientShortFormType;
use AppBundle\Form\OpfType;
use AppBundle\Form\ObjectFilterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;//for chices
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class ObjectController extends Controller {
    private $listFilter = [];
    /**
     * @Route("/objects", name="crm_object_list")
     */
    public function indexAction(Request $request)
    {
        //render params form
        $filterType = isset($this->listFilter['type']) ? $this->listFilter['type'] : '';
        $paramsForm = $this->getParamsFilterForm([],$filterType);
        $paramsForm->handleRequest($request);
        $filterData = $paramsForm->getData();

        if($filterData)
            $filter = $this->prepareFilterParams($filterData);
        else 
            $filter = $this->prepareFilterParams();

        $query = $this->getDoctrine()->getRepository('AppBundle:Object')->getFilteredObjects($filter);
//        dump($query);
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );
        

        return $this->render('crm/object/list.html.twig', array(
            'objects' => $result,
            'paramsForm' => $paramsForm->createView(),
        ));
    }
    /**
     * @Route("/objects/simlpe/rent", name="crm_object_list_rent")
     */
    public function indexRentAction(Request $request){
        $this->listFilter['type'] = 'simple_rent';
        return $this->indexAction($request);
    }
    /**
     * @Route("/objects/simlpe/sale", name="crm_object_list_sale")
     */
    public function indexSaleAction(Request $request){
        $this->listFilter['type'] = 'simple_sale';
        return $this->indexAction($request);
    }
    /**
     * @Route("/objects/com/sale", name="crm_object_list_com_sale")
     */
    public function indexComSaleAction(Request $request){
        $this->listFilter['type'] = 'comercial_sale';
        return $this->indexAction($request);
    }
    /**
     * @Route("/objects/com/rent", name="crm_object_list_com_rent")
     */
    public function indexComRentAction(Request $request){
        $this->listFilter['type'] = 'comercial_rent';
        return $this->indexAction($request);
    }
    
    /**
     * @Route("/objects/user/{id}/checklastactive", name="crm_object_checklastactive_list")
     */
    public function checkLastActiveAction(User $user){
        $callObjects = $this->getDoctrine()->getRepository('AppBundle:Object')->getObjectsForCall($user);
        return $this->render('crm/object/_list_lastActive_ajax.html.twig',['items'=>$callObjects]);
    }
    
    /**
     * @Route("/objects/new", name="crm_object_new")
     */
    public function newAction(Request $request, FileUploader $fileUploader){
        $form = $this->createForm(ObjectFormType::class);

        //render params form
        $paramsForm = $this->getParamsForm([]);
        $paramsForm->handleRequest($request);
        
        $clientForm = $this->createForm(ClientShortFormType::class);
        $clientForm->handleRequest($request);
        
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $object = $form->getData();
            
            //attach new client
            if($request->get('new_client','N')=='Y'){
                $client = $clientForm->getData();
                $client->setOwner(true);
                if($object->getUser())
                    $client->setUser($object->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
                $object->setClient($client);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();
            
            if($object->getCode() == ""){
                $object->setCode($object->getId());
                $em->persist($object);
                $em->flush();
            }
            //work with file
            $files = $object->getPhotos();
            if($files){
                $fileUploader->setCurrentDir($this->getParameter('objects_photo_directory').'/'.$object->getId());
                $fileNames = $fileUploader->multipleUpload($files);
                $object->setPhotos($fileNames);
                $em->persist($object);
                $em->flush();
            }
            
            //save params
//            $paramsForm = $this->getParamsForm($object);
//            $paramsForm->handleRequest($request);
            $this->saveFormParams($object,$paramsForm->getData());
            
            $this->addFlash('success', 'Object created!');

            if($request->get('submitType')!='apply')
                return $this->redirectToRoute('crm_object_list');
            else
                return $this->redirectToRoute('crm_object_edit',['id'=>$object->getId()]);
        }

        return $this->render('crm/object/new.html.twig', [
            'form' => $form->createView(),
            'paramsForm' => $paramsForm->createView(),
            'clientForm' => $clientForm->createView(),
            'object' => null
        ]);
    }
    
    /**
     * @Route("/objects/{id}/edit", name="crm_object_edit")
     */
    public function editAction(Request $request, Object $object, FileUploader $fileUploader){
//        if(file_exists($this->getParameter('user_photo_directory').'/'.$object->getPhotos()) && is_file($this->getParameter('user_photo_directory').'/'.$object->getPhotos()))
        $object->setPhotos($object->getPhotos());
        $old_photos = $object->getPhotos();

        $form = $this->createForm(ObjectFormType::class, $object);
        $form->handleRequest($request);
        
        //render params form
        $objectformParams = $object->getParamsArrayMap(true);
        $paramsForm = $this->getParamsForm($objectformParams);
        $paramsForm->handleRequest($request);
        
        $clientForm = $this->createForm(ClientShortFormType::class);
        $clientForm->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $object = $form->getData();
            
            //attach new client
            if($request->get('new_client','N')=='Y'){
                $client = $clientForm->getData();
                $client->setOwner(true);
                if($object->getUser())
                    $client->setUser($object->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($client);
                $em->flush();
                $object->setClient($client);
            }
            
            //work with files
            //remove if need
            if($del_photos = $request->get('del_photo',false)){
                foreach($del_photos as $dp){
                    if(isset($old_photos[$dp])){
                        unlink($this->getParameter('assets_directory').$old_photos[$dp]);
                        unset($old_photos[$dp]);
                    }
                }
            }
            //upload
            $files = $object->getPhotos();
            if($files){
                $fileUploader->setCurrentDir($this->getParameter('objects_photo_directory').'/'.$object->getId());
                $fileNames = $fileUploader->multipleUpload($files);
                $object->setPhotos(array_merge($old_photos,$fileNames));
            } else {
                $object->setPhotos($old_photos);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();
            
            //save params
            $this->saveFormParams($object,$paramsForm->getData());

            $this->addFlash('success', 'Object updated!');

            if($request->get('submitType')!='apply')
                return $this->redirectToRoute('crm_object_list');
            else
                return $this->redirectToRoute('crm_object_edit',['id'=>$object->getId()]);
        }

        
        
        return $this->render('crm/object/edit.html.twig', [
            'form' => $form->createView(),
            'paramsForm' => $paramsForm->createView(),
            'clientForm' => $clientForm->createView(),
            'object' => $object
        ]);
    }
        
    /**
     * @Route("/objects/{id}/delete", name="crm_object_delete")
     */
    public function deleteAction(Request $request, Object $object){
        //check permissions if SuperUser Or Owner
        if (!$object) {
            throw $this->createNotFoundException('No object found');
        }
        //remove photos
        if(is_dir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$object->getId()))
            $this->deleteDir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$object->getId());
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($object);
        $em->flush();

        $this->addFlash('success', 'Object deleted!');
        
        return $this->redirectToRoute('crm_object_list');
    }
   
    /**
     * @Route("/objects/{id}/changestatus", name="crm_object_changestatus")
     */
    public function changeStatusAction(Request $request, Object $object){
        //check permissions if SuperUser Or Owner
        if (!$object) {
            throw $this->createNotFoundException('No object found');
        }
        //remove photos
//        if(is_dir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$object->getId()))
//            $this->deleteDir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$object->getId());
        
        $em = $this->getDoctrine()->getEntityManager();
        $object->setStatus($request->get('status','new'));
        $em->persist($object);
        $em->flush();

        $this->addFlash('success', 'Статус змінено');
        
        return $this->redirectToRoute('crm_object_list');
    }
    /**
     * @Route("/objects/{id}/changeadv", name="crm_object_changeadv")
     */
    public function changeAdvAction(Request $request, Object $object){
        //check permissions if SuperUser Or Owner
        if (!$object) {
            throw $this->createNotFoundException('No object found');
        }
        //remove photos
//        if(is_dir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$object->getId()))
//            $this->deleteDir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$object->getId());
        
        $em = $this->getDoctrine()->getEntityManager();
        if($request->get('domria','')!="")
            $object->setDomria($request->get('domria','0'));
        $em->persist($object);
        $em->flush();

//        $this->addFlash('success', 'Статус змінено');
        $data = [
                'code' => 1,
                'message' => 'Публікацію змінено'
            ];
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/objects/{id}/search_bid", name="crm_object_search_bid")
     */
    public function searchBidAction(Request $request, Object $object){
        //check permissions if SuperUser Or Owner
        if (!$object) {
            throw $this->createNotFoundException('No object found');
        }
        
        $queryString = [];
        
        $queryString['type'] = $object->getType();
//        $queryString['min_price'] = $bid->getMinPrice();
//        $queryString['max_price'] = $bid->getMaxPrice();
//        
        $params = $object->getParamsArrayMap();
        foreach($params as $param){
            switch($param['type']){
                case 'text': 
                        $queryString[$param['param_id']] = $param['val'];
                    break;
                case 'integer':
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

        $this->addFlash('success', "Фльтр заповнено згідно об'єкту №".$object->getId());
        
        return $this->redirectToRoute('crm_bid_list',['object'=>$object->getId(),'form'=>$queryString]);
    }
    
    /**
     * @Route("/objects/{id}", name="crm_object_show")
     */
    public function showAction(Request $request, Object $object){
        
        if (!$object) {
            throw $this->createNotFoundException('No object found');
        }
        
        //prepare params array
        $params = [];
        $objectParams = $object->getParams();
        foreach ($objectParams as $objectParam){
            $param = $objectParam->getParam();
            $multiple = $param->getMultiple();
            switch ($param->getType()){
                case 'diapazon': 
                case 'integer': $val = $objectParam->getNumber(); break;
                case 'text': $val = $objectParam->getString(); break;
                case 'select': $val = $objectParam->getProperty()->getName(); break;
            }
            if($multiple){
                $params[$param->getId()]['val'][] = $val;
            } else {
                $params[$param->getId()]['val'] = $val;
            }
            $params[$param->getId()]['name'] = $param->getName();
            $params[$param->getId()]['multiple'] = $multiple;
        }
        
        if($request->get('ajax','')=='Y'){
            $tmpl = 'crm/object/_show.html.twig';
        } else {
            $tmpl = 'crm/object/show.html.twig';
        }
        
        return $this->render($tmpl, array(
            'object' => $object,
            'params' => $params
        ));
    }
    
    public function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            dump($dirPath);
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
    
    /** delete empty params */
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
     * get params form for edit page
     */
    public function getParamsForm($objectformParams = []){
        //get params and Properties for build
        $formParams = $this->getParamsForForm();
        if(!empty($objectformParams)){
            //prepare params for insetion in form
            $objectformParams = $this->prepareFormParams($formParams, $objectformParams);
        }
        return $this->generateParamsForm($formParams, ['data'=>$objectformParams]);
    }
    
    /**
     * find all params created for objects (total params library)
     * @return array
     */
    public function getParamsForForm($forFilter = false,$filterType=''){
        $em = $this->getDoctrine()->getManager();
        if($forFilter)
            $params = $em->getRepository('AppBundle:Param')->findAllParamsForFilterForm($filterType);
        else
            $params = $em->getRepository('AppBundle:Param')->findAllParamsForForm();
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
            }
            $formParams[] = $formParam;
        }
        return $formParams;
    }
    
    public function prepareFormParams($formParams,$objectformParams){
        foreach ($formParams as $formParam){
            switch($formParam['type']){
                case 'text': 
                    break;
                case 'integer': 
                    break;
                case 'diapazon': 
                    if(isset($objectformParams[$formParam['id']])){
                        $objectformParams['min_'.$formParam['id']] = $objectformParams[$formParam['id']][0];
                        $objectformParams['max_'.$formParam['id']] = $objectformParams[$formParam['id']][1];
                    }
                    break;
                case 'select': 
                    break;
            }
        }
        return $objectformParams;
    }

    
    /**
     * get params for filter form and for repository queryBuilder
     */
    public function getParamsFilterFormParams($filterType = ''){
        $defParams = $this->addDefaultObjectSearchFields();
        $formParams = $this->getParamsForForm(true,$filterType);
        
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
        }
        $formParams = array_merge($defParams,$formParams);
        return $formParams;
    }
    /**
     * get filter form fot list of elements
     */
    public function getParamsFilterForm($data = [], $filterType = ''){
        $formParams = $this->getParamsFilterFormParams($filterType);
        
        return $this->generateParamsForm($formParams,['method'=>'GET','data'=>$data]);
    }
    
    public function addDefaultObjectSearchFields($formParams = []){
        $formParams[] = [
            'id' => 'status',
            'type' => 'select',
            'label' => 'Статус',
            'multiple' => false,
            'choices' => [
                'Всі'  => 'all',
                'В продажу'  => 'insale',
                'Зарезервовано'  => 'reserved',
                'Продано'  => 'saled',
                'В архіві'  => 'archive',
            ]
        ];
//        dump($this->listFilter);
        if(!isset($this->listFilter['type']))
            $formParams[] = [
                'id' => 'type',
                'type' => 'select',
                'label' => 'Тип',
                'multiple' => false,
                'choices' => ['Продаж' => 'simple_sale','Оренда'  => 'simple_rent','Комерція продаж'  => 'comercial_sale','Комерція оренда'  => 'comercial_rent',],
            ];
        $formParams[] = [
            'id' => 'location',
            'type' => 'select',
            'label' => 'Район',
            'multiple' => true,
            'choices' =>  $this->getDoctrine()->getRepository('AppBundle:Location')->getLocationsForFilter(),
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
            'id' => 'rooms',
            'type' => 'integer',
            'label' => 'К-сть кімнат',
            'multiple' => false,
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
        $formParams[] = [
            'id' => 'clientstr',
            'type' => 'text',
            'label' => 'Власник',
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
                case 'diapazon':
                        $formBuilder->add('min_'.$formParam['id'],IntegerType::class, [
                            'label' => $formParam['label'].' від'
                        ]);
                        $formBuilder->add('max_'.$formParam['id'],IntegerType::class, [
                            'label' => $formParam['label'].' до'
                        ]);
                    break;
                case 'hidden':
                        $formBuilder->add($formParam['id'],HiddenType::class, [
                            'label' => $formParam['label']
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
    
    public function saveFormParams(Object $object, $newobjectParams){
        $currentobjectParams = $object->getParamsArrayMap();
        $comparedParams = $this->compareOldAndNewParams($currentobjectParams,$newobjectParams);
        
        $formParams_ = $this->getParamsForForm();$formParams = [];
        //resort
        foreach($formParams_ as $fp){$formParams[$fp['id']] = $fp;}
        
        $em = $this->getDoctrine()->getEntityManager();
        if(!empty($comparedParams['delete'])){
            foreach($comparedParams['delete'] as $forDelete){
                $op = $this->getDoctrine()->getRepository('AppBundle:ObjectParam')->find($forDelete);
                $em->remove($op);
                $em->flush();
            }
        }
        
        if(!empty($comparedParams['update'])){
            foreach($comparedParams['update'] as $forUpdate){
                $op = $this->getDoctrine()->getRepository('AppBundle:ObjectParam')->find($forUpdate['id']);
                switch($forUpdate['type']){
                    case 'text': 
                        $op->setString($forUpdate['val']);
                        break;
                    case 'integer': 
                        $op->setNumber($forUpdate['val']);
                        break;
                    case 'select': 
                        $property = $this->getDoctrine()->getRepository('AppBundle:Property')->find($forUpdate['val']);
                        $op->setProperty($property);
                        break;
                }
                $em->persist($op);
                $em->flush();
            }
        }
        
        if(!empty($comparedParams['insert'])){
            foreach($comparedParams['insert'] as $forInsert){
                $op = new ObjectParam();
                $op->setObject($object);
                $param = $this->getDoctrine()->getRepository('AppBundle:Param')->find($forInsert['param_id']);
                $op->setParam($param);
                switch($formParams[$forInsert['param_id']]['type']){
                    case 'text': 
                        $op->setString($forInsert['val']);
                        break;
                    case 'integer': 
                    case 'diapazon': 
                        $op->setNumber($forInsert['val']);
                        break;
                    case 'select': 
                        $property = $this->getDoctrine()->getRepository('AppBundle:Property')->find($forInsert['val']);
                        $op->setProperty($property);
                        break;
                }
                $em->persist($op);
                $em->flush();
            }
        }        
    }
}