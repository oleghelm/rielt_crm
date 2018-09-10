<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Object;
use AppBundle\Entity\Param;
use AppBundle\Entity\ObjectParam;
use AppBundle\Entity\Location;
use AppBundle\Form\OpfType;
use AppBundle\Form\ObjectFilterFormType;
use AppBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Unirest;
/**
 */
class DomRiaParserController extends Controller {
    public $API_KEY = '2q62xWJLweTZYLePhocwyOZszrS9OCrsbf9Q3r8p';
    public $API_ID  = '5461082';
    public $ids = [];
    public $default_location;
    public $locations = [];
    public $domria_params_grupped = [];
    public $domria_params = [];
    public $params = [];
    /**
     * @Route("/parser/domria_get_objects", name="admin_parser_domria_get_objects")
     */
    public function parseObjectsAction()
    {
        $this->default_location = $this->getDoctrine()->getRepository('AppBundle:Location')->find(5);
        $this->locations[$this->default_location->getNameru()] = $this->default_location;
        
        $params = $this->getDoctrine()->getRepository('AppBundle:Param')->findAll();
        foreach($params as $param){
            $this->params[$param->getExportName()] = $param;
        }
        
        $this->getDomRiaParams();
        
        //get ids to parse
        $this->getListObjectsByFilter();
        if(!empty($this->ids)){
            $this->getCompareCurrentObjectsList();
        }
        $this->addObjects();
//        $d_object = $this->getDomRiaObjectByID(14769144);
//        $this->addDomriaObject(14769144,$d_object);
//        die;
            
//        dump($this->ids);
//        $this->getDomRiaObjectByID(14769144);
    }
    
    private function getListObjectsByFilter(){
        //curl query to domria
        //category=1 - kvartiru
        //category=4 - doma
        //category=13 - komrcial nedv
        //category=10 - ofisu
        //category=24 - zemelni uchastku
        //category=30 - garaji
        // search Songs of Frank Sinatra
        $check_ids = array(1);
        foreach($check_ids as $category_id){
            $headers = array('Accept' => 'application/json');
            $query = array(
                'category' => $category_id, 
                'realty_type' => '0',
                'operation_type' => '0',
                'state_id' => '4',
                'city_id' => '4',
                'district_id' => '0',
                'date_from' => '2018-09-05',
                'exclude_agencies' => '1',
                'api_key' => $this->API_KEY,
                );
            $response = Unirest\Request::get('https://developers.ria.com/dom/search',$headers,$query);
            if($response->body->count > 0)
                $this->ids = array_merge($this->ids,$response->body->items);
            
            if($response->body->count > 100){
                for($page = 1;$page<=($response->body->count-$response->body->count%100)/100;$page++){
                    $query['page'] = $page;
                    $response = Unirest\Request::get('https://developers.ria.com/dom/search',$headers,$query);
                    if($response->body->count > 0)
                        $this->ids = array_merge($this->ids,$response->body->items);
                }
            }
            
        }
    }
    
    private function getDomRiaParams(){
        $headers = array('Accept' => 'application/json');
        $query = array(
            'category' => 1,
            'realty_type' => 0,
            'operation_type' => 1,
            'api_key' => $this->API_KEY,
            );
        $response = Unirest\Request::get('https://developers.ria.com/dom/options',$headers,$query);
        $this->domria_params_grupped = $response->body;
        foreach ($this->domria_params_grupped as $gr_param){
            foreach($gr_param->items as $param){
                $this->domria_params[$param->characteristic_id] = $param;
            }
        }
    }
    private function getDomRiaObjectByID($id){
        $headers = array('Accept' => 'application/json');
        $query = array('api_key' => $this->API_KEY,);
        $response = Unirest\Request::get('https://developers.ria.com/dom/info/'.$id,$headers,$query);
//        dump($response->body);
        return $response->body;
    }
    
    private function getCompareCurrentObjectsList(){
        //compare current objects in db with ids in parsing list
        $query = $this->getDoctrine()->getRepository('AppBundle:Object')->getObjectsByDomriaIds($this->ids);
        $objects = $query;
        $issetIds = [];
        foreach ($objects as $object){
            //collect isset ids
            $issetIds[] = $object->getDomriaID();//array_search($object->getDomriaID(), $this->ids);
        }
        //removeisset ids from collection
        $this->ids = array_diff($this->ids,$issetIds);
    }
    
    private function addObjects(){
        $i=0;
        foreach ($this->ids as $id){
            $d_object = $this->getDomRiaObjectByID($id);
            $this->addDomriaObject($id,$d_object);
            $i++;
            if($i>=10)die;
        }
    }
    
    private function addDomriaObject($id,$d_object){
//        $data = $this->getSslPage('https://dom.ria.com/uk/'.$d_object->beautiful_url);
        
//        dump($data);die;
        //create object object
        $object = new Object();
        //set basic parameters
        if(isset($d_object->rooms_count))
            $object->setRooms($d_object->rooms_count);
        if(isset($d_object->description_uk))
            $object->setInfo($d_object->description_uk);
        if(isset($d_object->total_square_meters))
            $object->setArea($d_object->total_square_meters);
        $object->setDomriaId($id);
        $object->setCreated(new \DateTime('now'));
        $object->setLastUpdate(new \DateTime('now'));
        $object->setStatus('insale');
//        $object->setCompany(...);
//        $object->setClient(...);
//        $object->setUser(...);
        
        if($d_object->is_commercial == 0)
            $type = 'simple_';
        else
            $type = 'comercial_';
        
        if($d_object->advert_type_name == 'продажа')
            $type .= 'sale';
        else
            $type .= 'rent';
        $object->setType($type);
//        dump($d_object);
        if($d_object->currency_type == '$'){
            $object->setBasePrice('price');
            $object->setPrice($d_object->price);
            $object->setPriceM2(round($d_object->price/$d_object->total_square_meters));
        } else {
            $object->setBasePrice('price_uah');
            $object->setPriceUah($d_object->price);
            $object->setPriceM2Uah(round($d_object->price/$d_object->total_square_meters));
        }
        
        $addr = '';
        if(isset($d_object->state_name) && $d_object->state_name!="")
            $addr .= 'обл. '.$d_object->state_name.', ';
        if(isset($d_object->city_name) && $d_object->city_name!="")
            $addr .= 'м. '.$d_object->city_name.', ';
        if(isset($d_object->district_name) && $d_object->district_name!="")
            $addr .= 'р. '.$d_object->district_name.', ';
        if(isset($d_object->street_name) && $d_object->street_name!="")
            $addr .= 'вул. '.$d_object->street_name.', ';
        if(isset($d_object->building_number_str) && $d_object->building_number_str!="")
            $addr .= 'б. '.$d_object->building_number_str.', ';
        $object->setAddress($addr);
        $object->setName('DOM.RIA '.$id.' '.$d_object->advert_type_name.' '.$d_object->realty_type_name.' '.$addr);
        
        if(isset($d_object->district_name))
            $object->setLocation($this->getLocation($d_object->district_name));
        else
            $object->setLocation($this->getLocation('set_default'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
        
        //set images
        if(!empty($d_object->photos)){
            //make photopath
            $tmp = explode("-",$d_object->beautiful_url);
            unset($tmp[count($tmp)-1]);
            unset($tmp[0]);
            $photos = $this->getImages($d_object->photos,$object->getId(),'https://cdn.riastatic.com/photosnew/dom/photo/'.implode('-',$tmp).'__');
            if(!empty($photos))
                $object->setPhotos($photos);
            $em->persist($object);
            $em->flush();
        }
        
        //set parameters
        $preset_params = [
            'source_link'=>'https://dom.ria.com/uk/'.$d_object->beautiful_url
        ];
        $this->makeParamsSaveArray($d_object,$object,$preset_params);
//        dump($object);die;
    }
    
    private function getImages($images = [], $id = 'test',$preurl){
        $result = [];
        $photos = [];
        foreach ($images as $photo){
            //download photo and attach file to object
            list($a,$format) = explode('.',$photo->file);
            $photos[] = $preurl.$photo->id.'fl.'.$format;
        }
        if(!is_dir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$id)){
            mkdir($this->getParameter('assets_directory').$this->getParameter('objects_photo_directory').'/'.$id);
        }
        $fileUploader = new FileUploader($this->getParameter('assets_directory'));
        $fileUploader->setCurrentDir($this->getParameter('objects_photo_directory').'/'.$id);
        $fileNames = $fileUploader->multipleUploadFromUrl($photos);
        return $fileNames;
    }


    private function makeParamsSaveArray($d_object,$object=false,$result = []){
//        dump($d_object);
//        dump($this->params);
//        dump($this->domria_params);
//        $result = [];
        //make array field - value
        foreach($d_object->characteristics_values as $k=>$v){
//            $this->domria_params[$k];
            if(isset($this->domria_params[$k]->field_name)){
                $field_name = $this->domria_params[$k]->field_name;
                if(isset($this->domria_params[$k]->children) && !empty($this->domria_params[$k]->children)){
                    $value = $this->domria_params[$k]->children->$v->name;
                } else {
                    $value = $d_object->$field_name;
                }
                $result[$field_name] = $value;
            }
        }
        if(isset($result['floors_count'])){$result['floors'] = $result['floors_count'];unset($result['floors_count']);}
        if(isset($result['total_square_meters'])){$result['total_area'] = $result['total_square_meters'];unset($result['total_square_meters']);}
        if(isset($result['living_square_meters'])){$result['living_area'] = $result['living_square_meters'];unset($result['living_square_meters']);}
        if(isset($result['kitchen_square_meters'])){$result['kitchen_area'] = $result['kitchen_square_meters'];unset($result['kitchen_square_meters']);}
        if(isset($result['is_bargain'])){$result['bargain'] = 'да';unset($result['is_bargain']);}
        if(isset($result['is_exchange'])){$result['exchange'] = $result['is_exchange'];unset($result['is_exchange']);}
        if(isset($result['rooms_count']))unset($result['rooms_count']);
        if(isset($result['price']))unset($result['price']);
        if(isset($result['currency_type']))unset($result['currency_type']);
        if(isset($result['price_type']))unset($result['price_type']);
//        dump($result);
//        dump($this->domria_params);
        
        if($object){
            $em = $this->getDoctrine()->getManager();
            foreach($result as $param_code => $param_value){
                if(isset($this->params[$param_code])){
                    $op = new ObjectParam();
                    $op->setObject($object);
                    $op->setParam($this->params[$param_code]);
                    switch($this->params[$param_code]->getType()){
                        case 'text': 
                            $op->setString($param_value);
                            break;
                        case 'integer': 
                        case 'diapazon': 
                            $op->setNumber($param_value);
                            break;
                        case 'floatdiapazon': 
                        case 'float': 
                            $op->setFloatnumber(floatval($param_value));
                            break;
                        case 'select': 
                            $property = $this->getParamsProperty($this->params[$param_code], $param_value);
                            if(!$property)continue;
                            $op->setProperty($property);
                            break;
                    }
                    $em->persist($op);
                    $em->flush();
                }
            }
        }
    }
    
    private function getParamsProperty($param,$search_name){
        $property = $this->getDoctrine()->getRepository('AppBundle:Property')->findPropertyByExportName($param,$search_name);
        return $property;
    }
    
    private function getLocation($ename){
        if(!isset($this->locations[$ename])){
            $location = $this->getDoctrine()->getRepository('AppBundle:Location')->getLocationByexportName($ename);
            if($location){
                $this->locations[$ename] = $location;
            }
            else 
                $this->locations[$ename] = $this->default_location;
        }
        return $this->locations[$ename];
    }
    
    private function getSslPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        
    }
}