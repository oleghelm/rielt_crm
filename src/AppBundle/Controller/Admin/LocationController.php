<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Location;
use AppBundle\Form\LocationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("is_granted('ROLE_ADMIN')")
 * @Route("/admin")
 */
class LocationController extends Controller {
    /**
     * @Route("/locations", name="admin_location_list")
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Location')->findAll();
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );

        return $this->render('admin/location/list.html.twig', array(
            'locations' => $result,
        ));
    }
    
    /**
     * @Route("/locations/level", name="admin_location_level_0")
     */
    public function indexLevelAction(Request $request)
    {
        $query = $this->getDoctrine()->getRepository('AppBundle:Location')->findBy(['level'=>0]);
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20)
        );

        return $this->render('admin/location/list.html.twig', array(
            'locations' => $result,
        ));
    }
    
    /**
     * @Route("/locations/new", name="admin_location_new")
     */
    public function newAction(Request $request)
    {
        if($request->get('location_id','')!=""){
            $_location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($request->get('location_id',''));
            $location = new Location();
            $location->setLocation($_location);
            $location->setLevel($_location->getLevel() + 1);
            dump($_location);
        } else {$location = null;}
        $form = $this->createForm(LocationFormType::class,$location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'Location created!');

            if($request->get('submitType')!='apply')
                if($location->getLocation())
                    return $this->redirectToRoute('admin_location_show',['id'=>$location->getLocation()->getId()]);
                else
                    return $this->redirectToRoute('admin_location_list');
            else
                return $this->redirectToRoute('admin_location_edit',['id'=>$location->getId(),'location'=>$location->getLocation()->getId()]);
        }

        return $this->render('admin/location/new.html.twig', [
            'form' => $form->createView(),
            'location' => null
        ]);
    }
    
    /**
     * @Route("/locations/{id}/edit", name="admin_location_edit")
     */
    public function editAction(Request $request, Location $location)
    {

        $form = $this->createForm(LocationFormType::class, $location);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'Location updated!');

            if($request->get('submitType')!='apply')
                if($location->getLocation())
                    return $this->redirectToRoute('admin_location_show',['id'=>$location->getLocation()->getId()]);
                else
                    return $this->redirectToRoute('admin_location_list');
            else
                return $this->redirectToRoute('admin_location_edit',['id'=>$location->getId(),'location'=>$location->getLocation()->getId()]);
        }

        return $this->render('admin/location/edit.html.twig', [
            'form' => $form->createView(),
            'location' => $location
        ]);
    }
        
    /**
     * @Route("/locations/{id}/delete", name="admin_location_delete")
     */
    public function deleteAction(Request $request, Location $location){
        //check permissions if SuperUser Or Owner
        if (!$location) {
            throw $this->createNotFoundException('No location found');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($location);
        $em->flush();

        $this->addFlash('success', 'Location deleted!');
        
        return $this->redirectToRoute('admin_location_list');
    }
    
    /**
     * @Route("/location/{id}", name="admin_location_show")
     */
    public function showAction(Request $request, Location $location){
        
        if (!$location) {
            throw $this->createNotFoundException('No location found');
        }
        
//        $sub_locations = 
        return $this->render('admin/location/show.html.twig', array(
            'location' => $location,
        ));
    }
}