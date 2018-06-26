<?php

namespace AppBundle\Controller\Crm;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Form\ClientFormType;
use AppBundle\Form\ClientFilterFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Component\HttpFoundation\File\File;
//use AppBundle\Service\FileUploader;
/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class ClientController extends Controller
{
    /**
     * @Route("/clients", name="crm_client_list")
     */
    public function indexAction(Request $request)
    {
//        $em    = $this->get('doctrine.orm.entity_manager');
        
//        $dql   = "SELECT c FROM AppBundle:Client c";
//        $query = $em->createQuery($dql);
        
//        $queryBuilder = $em->getRepository('AppBundle:Client')->createQueryBuilder('cl');
//        if($request->query->getAlnum('filter')){
//            $queryBuilder->where('cl.name LIKE :title')
//                    ->setParameter('title','%'.$request->query->getAlnum('filter','').'%');
//        }
//        $query = $queryBuilder->getQuery();
        //saved filter
        if (isset($_COOKIE['client_lastpage'])) {
            unset($_COOKIE['client_lastpage']);
            setcookie('client_lastpage', null, -1, '/');
        }
        setcookie("client_lastpage", $request->getRequestUri(),time()+3600,'/');
        $filter = $request->get('client_filter_form','');
        
        $form = $this->createForm(ClientFilterFormType::class,null,['method'=>'GET']);
        $form->handleRequest($request);
        
        $query = $this->getDoctrine()->getRepository('AppBundle:Client')->getFilteredClients($filter);
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 20)/*limit per page*/
        );

        return $this->render('crm/client/list.html.twig', array(
            'clients' => $result,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/clients/new", name="crm_client_new")
     */
    public function newAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $client = new Client();
        $client->setUser($user);
        $form = $this->createForm(ClientFormType::class,$client);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            
            //work with file
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client created!');

            if($_COOKIE['client_lastpage']!="")
                return $this->redirect($_COOKIE['client_lastpage']);
            else
                return $this->redirectToRoute('crm_client_list');
        }

        return $this->render('crm/client/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/clients/{id}/edit", name="crm_client_edit")
     */
    public function editAction(Request $request, Client $client)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$client->canEdit($user)){
            return $this->redirectToRoute('no_access',['ajax'=>'Y']);
        }
        $form = $this->createForm(ClientFormType::class, $client);
        
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client updated!');

            if($request->get('ajax')=='Y')
                return $this->redirectToRoute('crm_client_edit',['id'=>$client->getId(),'ajax'=>'Y']);
            else
                if($_COOKIE['client_lastpage']!="")
                    return $this->redirect($_COOKIE['client_lastpage']);
                else
                    return $this->redirectToRoute('crm_client_list');
        }

        if($request->get('ajax')=='Y')
            $tmpl = 'crm/client/edit_ajax.html.twig';
        else
            $tmpl = 'crm/client/edit.html.twig';
        
        return $this->render($tmpl, [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/clients/{id}/delete", name="crm_client_delete")
     */
    public function deleteAction(Request $request, Client $client){
        //check permissions if SuperUser Or Owner
        if (!$client) {
            throw $this->createNotFoundException('No client found');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        
        //unlink user from tables
        $em->getRepository('AppBundle:Client')->unlinkClientFromEntitys($client);
        
        $em->remove($client);
        $em->flush();

        $this->addFlash('success', 'Client deleted!');
        
        if($_COOKIE['client_lastpage']!="")
            return $this->redirect($_COOKIE['client_lastpage']);
        else
            return $this->redirectToRoute('crm_client_list');
    }
    
    /**
     * @Route("/clients/{id}/toarchive", name="crm_client_toarchive")
     */
    public function toarchiveAction(Request $request, Client $client){
        //check permissions if SuperUser Or Owner
        if (!$client) {
            throw $this->createNotFoundException('No client found');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $client->setStatus('archive');
        $em->persist($client);
        $em->flush();

        $this->addFlash('success', 'Клієнт перенесений в архів');
        
        return $this->redirectToRoute('crm_client_list');
    }
    
    /**
     * @Route("/clients/{id}", name="crm_client_show")
     */
    public function showAction(Request $request, Client $client){
        
        if (!$client) {
            throw $this->createNotFoundException('No client found');
        }

        $isAjax = $request->get("ajax",'N');
        if($isAjax=='Y'){
            return $this->render('crm/client/_show.html.twig', array(
                'client' => $client,
            ));
        } else {
            return $this->render('crm/client/show.html.twig', array(
                'client' => $client,
            ));
        }
    }
}