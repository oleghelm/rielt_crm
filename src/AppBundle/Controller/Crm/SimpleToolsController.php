<?php

namespace AppBundle\Controller\Crm;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\User;
use AppBundle\Entity\Object;
use AppBundle\Entity\Bid;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class SimpleToolsController extends Controller
{
    /**
     * @Route("/lastActive", name="crm_tools_lastActive")
     */
    public function lastActiveAction(Request $request)
    {
        $form = $this->getLastActiveFilter();
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $filter = $form->getData();
            foreach ($filter as &$f){$f = ['val'=>$f];}
            
            if($filter['entity']['val']=='object'){
                $query = $this->getDoctrine()->getRepository('AppBundle:Object')->getFilteredObjects($filter);
            }elseif($filter['entity']['val']=='bid'){
                $query = $this->getDoctrine()->getRepository('AppBundle:Bid')->getFilteredBids($filter);
            }
//            $result = null;
//
            $paginator  = $this->get('knp_paginator');
            $result = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 20)
            );
            
        } else {
            $result = null;
        }
        
        // replace this example code with whatever you need
        return $this->render('crm/tools/lastActive.html.twig', [
            'items' => $result,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/lastActive/object/{id}/updateToToday", name="crm_tools_lastActive_update_object")
     */
    public function updateObjectLastupdateAction(Request $request, Object $object){
        $em = $this->getDoctrine()->getEntityManager();
        $object->setLastUpdate(new \DateTime());
        $em->persist($object);
        $em->flush();
        
        $client = $object->getClient();
        $client->setLastUpdate(new \DateTime());
        $em->persist($client);
        $em->flush();
        
        if($request->get('ajax','N')=='Y'){
            $data = [
                'code' => 1,
                'message' => 'Дату змінено'
            ];
            return new JsonResponse($data);
        }else{
            return $this->redirect($request->get('return_url'));
        }
    }
    /**
     * @Route("/lastActive/bid/{id}/updateToToday", name="crm_tools_lastActive_update_bid")
     */
    public function updateBidLastupdateAction(Request $request, Bid $bid){
        $em = $this->getDoctrine()->getEntityManager();
        $bid->setLastUpdate(new \DateTime());
        $em->persist($bid);
        $em->flush();
        if($request->get('ajax','N')=='Y'){
            $data = [
                'code' => 1,
                'message' => 'Дату змінено'
            ];
            return new JsonResponse($data);
        }else{
            return $this->redirect($request->get('return_url'));
        }
    }
    
    public function getLastActiveFilter(){
        $formBuilder = $this->createFormBuilder(null,['method'=>'GET']);
        $formBuilder->add('lastUpdateStart',DateType::class,[
                    'label' => 'Останній контакт від',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ]);
        $formBuilder->add('lastUpdateEnd',DateType::class,[
                    'label' => 'Останній контакт до',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ]);
        $formBuilder->add('entity',ChoiceType::class, [
                'label' => 'Де шукати',
                'choices' => [
                    "В об'єктах"  => 'object',
                    'В заявках'  => 'bid',
                    ]
            ]);
        $formBuilder->add('user',EntityType::class,[
                'placeholder' => 'Виберіть кому',
                'class' => User::class,
                'query_builder' => function(UserRepository $repo) {
                    return $repo->findAllUsers();
                }
            ]);
        return $formBuilder->getForm();
                
    }
}
