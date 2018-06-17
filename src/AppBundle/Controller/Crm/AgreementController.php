<?php

namespace AppBundle\Controller\Crm;

use AppBundle\Entity\Agreement;
use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Entity\Object;
use AppBundle\Form\AgreementFormType;
use AppBundle\Form\AgreementFilterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class AgreementController extends Controller
{
    /**
     * @Route("/agreements", name="crm_agreement_list")
     */
    public function indexAction(Request $request)
    {
        
        $filter = $request->get('agreement_filter_form','');
        
        $form = $this->createForm(AgreementFilterFormType::class,null,['method'=>'GET']);
        $form->handleRequest($request);
        
        $query = $this->getDoctrine()->getRepository('AppBundle:Agreement')->getFiltered($filter);
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 20)/*limit per page*/
        );

        return $this->render('crm/agreement/list.html.twig', array(
            'items' => $result,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/agreements/new", name="crm_agreement_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(AgreementFormType::class);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            
            //work with file
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Agreement created!');

            return $this->redirectToRoute('crm_agreement_list');
        }

        return $this->render('crm/agreement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/agreements/{id}/edit", name="crm_agreement_edit")
     */
    public function editAction(Request $request, Agreement $agreement)
    {
        $form = $this->createForm(AgreementFormType::class, $agreement);
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $agreement = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($agreement);
            $em->flush();

            $this->addFlash('success', 'Agreement updated!');

            if($request->get('ajax')=='Y')
                return $this->redirectToRoute('crm_agreement_edit',['id'=>$agreement->getId(),'ajax'=>'Y']);
            else
                return $this->redirectToRoute('crm_agreement_list');
        }

        if($request->get('ajax')=='Y')
            $tmpl = 'crm/agreement/edit_ajax.html.twig';
        else
            $tmpl = 'crm/agreement/edit.html.twig';
        
        return $this->render($tmpl, [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/agreements/{id}/delete", name="crm_agreement_delete")
     */
    public function deleteAction(Request $request, Agreement $agreement){
        //check permissions if SuperUser Or Owner
        if (!$agreement) {
            throw $this->createNotFoundException('Угода не знайдена');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        //unlink user from tables
//        $em->getRepository('AppBundle:Client')->unlinkClientFromEntitys($agreement);
        
        $em->remove($agreement);
        $em->flush();

        $this->addFlash('success', 'Угода видалена');
        
        return $this->redirectToRoute('crm_agreement_list');
    }
    
    /**
     * @Route("/agreements/{id}/toarchive", name="crm_agreement_toarchive")
     */
    public function toarchiveAction(Request $request, Agreement $agreement){
        if (!$agreement) {
            throw $this->createNotFoundException('Угода не знайдена');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $agreement->setStatus('archive');
        $em->persist($agreement);
        $em->flush();

        $this->addFlash('success', 'Угода перенесена в архів');
        
        return $this->redirectToRoute('crm_agreement_list');
    }
    
    /**
     * @Route("/agreements/{id}", name="crm_agreement_show")
     */
    public function showAction(Request $request, Agreement $agreement){
        
        if (!$agreement) {
            throw $this->createNotFoundException('No agreement found');
        }

        $isAjax = $request->get("ajax",'N');
        if($isAjax=='Y'){
            return $this->render('crm/agreement/_show.html.twig', array(
                'agreement' => $agreement,
            ));
        } else {
            return $this->render('crm/agreement/show.html.twig', array(
                'agreement' => $agreement,
            ));
        }
    }
}