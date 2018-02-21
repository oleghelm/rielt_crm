<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Param;
use AppBundle\Form\ParamFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("is_granted('ROLE_ADMIN_PANEL_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/admin")
 */
class ParamController extends Controller {
    /**
     * @Route("/params", name="admin_param_list")
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Param')->findAllParams();
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );

        return $this->render('admin/param/list.html.twig', array(
            'params' => $result,
        ));
    }
    
    /**
     * @Route("/params/new", name="admin_param_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(ParamFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $param = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($param);
            $em->flush();

            $this->addFlash('success', 'Param created!');

            if($request->get('submitType')!='apply')
                return $this->redirectToRoute('admin_param_list');
            else
                return $this->redirectToRoute('admin_param_edit',['id'=>$param->getId()]);
        }

        return $this->render('admin/param/new.html.twig', [
            'form' => $form->createView(),
            'param' => null
        ]);
    }
    
    /**
     * @Route("/params/{id}/edit", name="admin_param_edit")
     */
    public function editAction(Request $request, Param $param)
    {

        $form = $this->createForm(ParamFormType::class, $param);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $param = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($param);
            $em->flush();

            $this->addFlash('success', 'Param updated!');

            if($request->get('submitType')!='apply')
                return $this->redirectToRoute('admin_param_list');
            else
                return $this->redirectToRoute('admin_param_edit',['id'=>$param->getId()]);
        }

        return $this->render('admin/param/edit.html.twig', [
            'form' => $form->createView(),
            'param' => $param
        ]);
    }
        
    /**
     * @Route("/params/{id}/delete", name="admin_param_delete")
     */
    public function deleteAction(Request $request, Param $param){
        //check permissions if SuperUser Or Owner
        if (!$param) {
            throw $this->createNotFoundException('No param found');
        }
        
//        $properties = $param->getProperties();
//        foreach($properties as $property){
//            $property->delete();
//        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($param);
        $em->flush();

        $this->addFlash('success', 'Param deleted!');
        
        return $this->redirectToRoute('admin_param_list');
    }
    
    /**
     * @Route("/param/{id}", name="admin_param_show")
     */
    public function showAction(Request $request, Param $param){
        
        if (!$param) {
            throw $this->createNotFoundException('No param found');
        }
        
        return $this->render('admin/param/show.html.twig', array(
            'param' => $param,
        ));
    }
}