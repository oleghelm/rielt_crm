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
    /**
     * @Route("/export/domria", name="admin_expotr_domria_object_list")
     */
    public function indexAction(Request $request)
    {
        $filter = ['domria'=>['val'=>true]];
        $query = $this->getDoctrine()->getRepository('AppBundle:Object')->getFilteredObjects($filter);
//        dump($query);
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
        //load all objects
        $filter = ['domria'=>['val'=>true]];
        $items = $this->getDoctrine()->getRepository('AppBundle:Object')->getFilteredObjects($filter)->execute();
//        $arItems = [];
        $export = '<?xml version="1.0" encoding="UTF-8"?><realties xmlns="http://dom.ria.ua/xml/xsd/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://dom.ria.ua/xml/xsd/ http://dom.ria.ua/xml/xsd/dom.xsd">';
        $datetime = new \DateTime('now');
        $export .= '<generation_date>'.$datetime->format(\DateTime::ATOM).'</generation_date>';
        foreach($items as $item):
            $xml = '<realty>';
            $xml .= '<email>'.'oleg123hedsiuk@gmail.com'.'</email>';
            $xml .= '<local_realty_id>'.$item->getId().'</local_realty_id>';
            $xml .= '<realty_type>'.$item->getRealty().'</realty_type>';
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
                    $xml .= '<loc crc="'.md5_file($this->getParameter('assets_directory').'/'.$photo).'">'.$photo.'</loc>';
                }
                $xml .= '</photos_urls>';
            }
            $params = $this->getDoctrine()->getRepository('AppBundle:Object')->getExportParams($item);
            if(!empty($params)){
                $arItem['characteristics'] = [];
                $arParams = [];
                foreach($params as $param){
                    $parent = $param->getParam();
                    if($parent->getExportName()!=""){
                        $arParams[$parent->getId()]['basic'] = $parent->getBasicParam();
                        $arParams[$parent->getId()]['code'] = $parent->getExportName();
                        switch ($parent->getType()){
                            case 'select': $arParams[$parent->getId()]['values'][] = $param->getProperty()->getName();break;
                            case 'text': $arParams[$parent->getId()]['values'][] = $param->getString(); break;
                            case 'integer': $arParams[$parent->getId()]['values'][] = $param->getNumber(); break;
                        }
                    }
                }
                $characteristics = '';
                foreach($arParams as $arParam){
                    if($arParam['basic']){
                        $xml .= '<'.$arParam['code'].'>'.implode(', ',$arParam['values']).'</'.$arParam['code'].'>';
                    } else {
                        $characteristics .= '<'.$arParam['code'].'>'.implode(', ',$arParam['values']).'</'.$arParam['code'].'>';
                    }
                }
                if($characteristics!="")
                    $xml .= '<characteristics>'.$characteristics.'</characteristics>';
            }
            $xml .= '</realty>';
            $export .= $xml;
        endforeach;
        $export .= '</realties>';
        
        $file = $this->getParameter('assets_directory').'/xml/domria.xml';
        file_put_contents($file, $export);
        
        $this->addFlash('success', 'Файл /xml/domria.xml оновлено! Переглянути можна за посиланням <a href="/xml/domria.xml" target="_blank">/xml/domria.xml</a>');
        return $this->redirectToRoute('admin_expotr_domria_object_list');
    }
    
}