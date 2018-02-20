<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParamsFormBuilder extends Controller {
    
    /**
     * get params form for edit page
     */
    public function getParamsForm($name = '', $objectformParams = []){
        //get params and Properties for build
        $formParams = $this->getParamsForForm($name);
        dump($formParams);
        dump($objectformParams);return;
        if(!empty($objectformParams)){
            $objectformParams = $this->prepareFormParams($formParams, $objectformParams);
        }
        
        return $this->generateParamsForm($formParams, ['data'=>$objectformParams]);
    }
    
    /**
     * find all params created for objects (total params library)
     * @return array
     */
    public function getParamsForForm($name = '', $forFilter = false){
        $em = $this->getDoctrine()->getManager();
        if($forFilter)
            $params = $em->getRepository('AppBundle:Param')->findAllParamsForFilterForm($name);
        else
            $params = $em->getRepository('AppBundle:Param')->findAllParamsForForm($name);
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
            $formParams[$param->getSort()] = $formParam;
        }
//        dump($formParams);
        return $formParams;
    }
}