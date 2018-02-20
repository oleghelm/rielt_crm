<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Property;
use AppBundle\Form\PropertyFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class PropertyController extends Controller {
    /**
     * @Route("/propertys", name="admin_property_list")
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Property')->findAllProperties();
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );

        return $this->render('admin/property/list.html.twig', array(
            'propertys' => $result,
        ));
    }
    
    /**
     * @Route("/propertys/new", name="admin_property_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(PropertyFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $property = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($property);
            $em->flush();

            $this->addFlash('success', 'Property created!');

            if($request->get('submitType')!='apply')
                if($request->get('return_url',"")!='')
                    return $this->redirect($request->get('return_url'));
                else
                    return $this->redirectToRoute('admin_property_list');
            else
                return $this->redirectToRoute('admin_property_edit',['id'=>$property->getId(),'param_id'=>$property->getParam()->getId(),'return_url'=>$request->get('return_url')]);
        }

        return $this->render('admin/property/new.html.twig', [
            'form' => $form->createView(),
            'property' => null
        ]);
    }
    
    /**
     * @Route("/propertys/{id}/edit", name="admin_property_edit")
     */
    public function editAction(Request $request, Property $property)
    {

        $form = $this->createForm(PropertyFormType::class, $property);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $property = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($property);
            $em->flush();

            $this->addFlash('success', 'Property updated!');

            if($request->get('submitType')!='apply')
                if($request->get('return_url',"")!='')
                    return $this->redirect($request->get('return_url'));
                else
                    return $this->redirectToRoute('admin_property_list');
            else
                return $this->redirectToRoute('admin_property_edit',['id'=>$property->getId(),'return_url'=>$request->get('return_url','')]);
        }

        return $this->render('admin/property/edit.html.twig', [
            'form' => $form->createView(),
//            'property' => $property
        ]);
    }
        
    /**
     * @Route("/propertys/{id}/delete", name="admin_property_delete")
     */
    public function deleteAction(Request $request, Property $property){
        //check permissions if SuperUser Or Owner
        if (!$property) {
            throw $this->createNotFoundException('No property found');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($property);
        $em->flush();

        $this->addFlash('success', 'Property deleted!');
        if($request->get('return_url',"")!='')
            return $this->redirect($request->get('return_url'));
        else
            return $this->redirectToRoute('admin_property_list');
    }
    
    /**
     * @Route("/propertys/{id}", name="admin_property_show")
     */
    public function showAction(Request $request, Property $property){
        
        if (!$property) {
            throw $this->createNotFoundException('No property found');
        }
        
        return $this->render('admin/property/show.html.twig', array(
            'property' => $property,
        ));
    }
}