<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Object;
use AppBundle\Entity\ObjectParam;
use AppBundle\Form\OpfType;
use AppBundle\Form\ObjectFilterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("is_granted('ROLE_ADMIN_PANEL_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/admin")
 */
class ExportController extends Controller {
    public $errIds = [];
    /**
     * @Route("/export/domria", name="admin_expotr_domria_object_list")
     */
    public function indexAction(Request $request)
    {
        $filter = ['domria'=>['val'=>true]];
        $query = $this->getDoctrine()->getRepository('AppBundle:Object')->getFilteredObjects($filter);
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );
        
        return $this->render('admin/export/domria.html.twig', array(
            'objects' => $result,
        ));
    }
    /**
     * @Route("/export/domria/go", name="admin_expotr_domria_go")
     */
    public function exportToDomria(Request $request){
        $url = $request->getSchemeAndHttpHost();
        //load all objects
        $filter = ['domria'=>['val'=>true]];
        $items = $this->getDoctrine()->getRepository('AppBundle:Object')->getFilteredObjects($filter)->execute();
        
        $export = '<?xml version="1.0" encoding="UTF-8"?><realties xmlns="http://dom.ria.ua/xml/xsd/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://dom.ria.ua/xml/xsd/ http://dom.ria.ua/xml/xsd/dom.xsd">';
        $datetime = new \DateTime('now');
        $export .= '<generation_date>'.$datetime->format(\DateTime::ATOM).'</generation_date>';
        foreach($items as $item):
//            if(!$item->getLocation()){continue;}
            $xml = '<realty>';
            $tags = ['location','rooms_count'];
            $realty_type = '';
            if($item->getCompany())
                $xml .= '<email>'.$item->getCompany()->getEmail().'</email>';
            $xml .= '<local_realty_id>'.$item->getId().'</local_realty_id>';
            $xml .= '<advert_type>'.((strpos($item->getType(),'sale')) ? 'продажа' : 'долгосрочная аренда').'</advert_type>';
            $xml .= '<description>'.$item->getOfficialinfo().'</description>';
            $xml .= '<realty_sale_type>'.$item->getSaletype().'</realty_sale_type>';
//            $xml .= ''.'';
            if($item->getLocation()){
                $location = $item->getLocation();
                $xml .= '<district>'.($location->getNameru() != "" ? $location->getNameru() : $location->getName()).'</district>';
                if($city = $location->getLocation()){
                    $xml .= '<city>'.($city->getNameru() != "" ? $city->getNameru() : $city->getName()).'</city>';
                    if($state = $city->getLocation()){
                        $xml .= '<state>'.($state->getNameru() != "" ? $state->getNameru() : $state->getName()).'</state>';
                    }
                }
            }
            if($item->getPhotos()){
                $xml .= '<photos_urls>';
                foreach($item->getPhotos() as $photo){
                    if(file_exists($this->getParameter('assets_directory').'/'.$photo))
                        $xml .= '<loc crc="'.md5_file($this->getParameter('assets_directory').'/'.$photo).'">'.$url.$photo.'</loc>';
                }
                $xml .= '</photos_urls>';
            }
            $characteristics = '';
            $characteristics .= '<rooms_count>'.$item->getRooms().'</rooms_count>';
            $characteristics .= '<total_area>'.$item->getArea().'</total_area>';
            switch ($item->getBaseprice()){
                case 'price_m2': $price = $item->getPriceM2(); $currency = '$'; $price_type = 'за кв.м.'; break;
                case 'price_m2_uah': $price = $item->getPriceM2Uah(); $currency = 'грн'; $price_type = 'за кв.м.'; break;
                case 'price_uah': $price = $item->getPriceUah(); $currency = 'грн'; $price_type = 'за объект'; break;
                default : $price = $item->getPrice(); $currency = '$'; $price_type = 'за объект'; break;
            }
            $characteristics .= '<price>'.$price.'</price>';
            $characteristics .= '<currency>'.$currency.'</currency>';
            $characteristics .= '<price_type>'.$price_type.'</price_type>';
            $params = $this->getDoctrine()->getRepository('AppBundle:Object')->getExportParams($item);
            if(!empty($params)){
                $arItem['characteristics'] = [];
                $arParams = [];
                foreach($params as $param){
                    $parent = $param->getParam();
                    if($parent->getExportName()!=""){
                        $arParams[$parent->getId()]['basic'] = $parent->getBasicParam();
                        $arParams[$parent->getId()]['code'] = $parent->getExportName();
                        $arParams[$parent->getId()]['id'] = $parent->getId();
                        switch ($parent->getType()){
                            case 'select': 
                                $val = $param->getProperty()->getExportName()!="" ? $param->getProperty()->getExportName() : $param->getProperty()->getName();
                                $arParams[$parent->getId()]['values'][] = $val;
                                if($arParams[$parent->getId()]['code']=='realty_type'){
                                    $realty_type = $param->getProperty()->getId();
                                }
                                break;
                            case 'text': $arParams[$parent->getId()]['values'][] = $param->getString(); break;
                            case 'integer': $arParams[$parent->getId()]['values'][] = $param->getNumber(); break;
                        }
                    }
                }
                foreach($arParams as $arParam){
                    if($arParam['basic']){
                        $xml .= '<'.$arParam['code'].'>'.implode(', ',$arParam['values']).'</'.$arParam['code'].'>';
                    } else {
                        $characteristics .= '<'.$arParam['code'].'>'.implode(', ',$arParam['values']).'</'.$arParam['code'].'>';
                    }
                    $tags[] = $arParam['code'];
                }
            }
            if($characteristics!="")
                    $xml .= '<characteristics>'.$characteristics.'</characteristics>';
            $xml .= '</realty>';
            if($this->validateObject($item->getId(),$realty_type,$tags))
                $export .= $xml;
        endforeach;
        $export .= '</realties>';
        if(!empty($this->errIds)){
            $this->addFlash('success', "Перевірте правильність заповнення параметрів в об'єктах з ID ".implode(", ",$this->errIds).". Решту об'єктів додано у файл обміну ".'<a href="/xml/domria.xml" target="_blank">/xml/domria.xml</a>');
        } else {
            $this->addFlash('success', 'Файл /xml/domria.xml оновлено! Переглянути можна за посиланням <a href="/xml/domria.xml" target="_blank">/xml/domria.xml</a>');
        }
        $file = $this->getParameter('assets_directory').'/xml/domria.xml';
        file_put_contents($file, $export);
        return $this->redirectToRoute('admin_expotr_domria_object_list');
    }
    public function validateObject($id, $realty_type = '', $tags = []){
        if($realty_type == '' || empty($tags)){return false;}
        $reqFields = [
            222 => ['location','total_area','floor','floors','wall_type','rooms_count'],//квартира
            223 => ['location','total_area','floor','floors'],//комната
            224 => ['location','total_area','floors','wall_type','rooms_count'],//дом
            225 => ['location','total_area','floors','wall_type','rooms_count'],//дом
            226 => ['location','total_area','floors','wall_type','rooms_count'],//дача
            227 => ['location','total_area','floor','floors','wall_type','rooms_count','object_type'],//офисное помещение
            228 => ['location','total_area','floor','floors','wall_type','rooms_count','object_type'],//офисное здание
            229 => ['location','total_area','floor','floors','rooms_count','object_type'],//торговые площади
            230 => ['location','total_area','rooms_count','object_type'],//складские помещения
            231 => ['location','total_area','floor','floors','rooms_count','object_type'],//производственные помещения
            232 => ['location','total_area','floor','floors','rooms_count'],//кафе, бар, ресторан
            233 => ['location','total_area','floor','floors','rooms_count'],//объект сферы услуг
            234 => ['location','total_area','floors'],//отель, гостиница
            235 => ['location','total_area','floors','rooms_count'],//база отдыха, пансионат
            236 => ['location','total_area','floor','floors','rooms_count'],//помещения свободного назначения
            237 => ['location','total_area','sphere'],//готовый бизнес
            238 => ['location','plot_area'],//участок под жилую застройку
            239 => ['location','plot_area'],//земля коммерческого назначения
            240 => ['location','plot_area'],//земля сельскохозяйственного назначения
            241 => ['location','plot_area'],//земля рекреационного назначения
            242 => ['location','plot_area'],//земля природно-заповедного назначения
            243 => ['location','total_area','cars','appointment'],//бокс в гаражном комплексе
            244 => ['location','total_area','cars','appointment'],//подземный паркинг
            245 => ['location','total_area','cars','appointment'],//место в гаражном кооперативе
            246 => ['location','total_area','cars','appointment'],//отдельно стоящий гараж
            247 => ['location','total_area','cars','appointment'],//место на стоянке
        ];
        foreach($reqFields[$realty_type] as $req){
            if(!in_array($req, $tags)){
                $this->errIds[] = $id;
//        dump($req);die;
                return false;
            }
        }
        return true;
    }
}