<?php

namespace AppBundle\Controller\Crm;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Ticket;
use AppBundle\Form\TicketFormType;
use AppBundle\Form\TicketFilterFormType;
use AppBundle\Form\TicketMyFilterFormType;
use AppBundle\Entity\User;
use AppBundle\Entity\Object;
use AppBundle\Entity\Bid;
use AppBundle\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class TicketController extends Controller
{
    /**
     * @Route("/tickets", name="crm_ticket_list")
     */
    public function listAction(Request $request)
    {
        
        $form = $this->createForm(TicketFilterFormType::class,null,['method'=>'GET']);
        $form->handleRequest($request);
        $filter = $form->getData();
        
        $query = $this->getDoctrine()->getRepository('AppBundle:Ticket')->getFilteredTickets($filter);
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 20)/*limit per page*/
        );
        
        // replace this example code with whatever you need
        return $this->render('crm/ticket/list.html.twig', [
            'items' => $result,
            'form' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/my_tickets", name="crm_myticket_list")
     */
    public function myListAction(Request $request)
    {
        
        $form = $this->createForm(TicketMyFilterFormType::class,null,['method'=>'GET']);
        $form->handleRequest($request);
        $filter = $form->getData();
        if(!$form->isSubmitted() && $form->get('date_type')->getData()=='day' && !$form->get('date_current')->getData()){
            $form->get('date_current')->setData(new \DateTime('now'));
        }
        if($filter['date_type']!='period' && !$filter['date_current']){
            $filter['date_type'] = 'day';
            $filter['date_current'] = new \DateTime('now');
        }
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $query = $this->getDoctrine()->getRepository('AppBundle:Ticket')->getFilteredUserTickets($user,$filter);
        
        $paginator  = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 20)/*limit per page*/
        );
        
        // replace this example code with whatever you need
        return $this->render('crm/ticket/mylist.html.twig', [
            'items' => $result,
            'form' => $form->createView()
        ]);
    }
    
        
    /**
     * @Route("/tickets/user/{id}", name="crm_user_tickets")
     * @Method("GET")
     */
    public function getUserTicketsAction(User $user){
        $today = $this->getDoctrine()->getRepository('AppBundle:Ticket')->getUserFromTodayTikets($user);
        $free = $this->getDoctrine()->getRepository('AppBundle:Ticket')->getFreeFromTodayTikets();
        
        $tickets = [];
        
        foreach($today as $ticket){
            $tickets['today'][] = [
                'id' => $ticket->getId(),
                'date' => $ticket->getDate()->format('d.m.Y'),
                'username' => $user->getName(),
                'time' => $ticket->getTime(),
                'task' => $ticket->getTaskTranslate(),
                'status_code' => $ticket->getStatus(),
                'status' => $ticket->getStatusTranslate(),
                'links' => [
                    'setstatus' => $this->generateUrl('crm_ticket_status', ['id' => $ticket->getId()]),
                    'show' => $this->generateUrl('crm_ticket_show', ['id' => $ticket->getId()]),
                    'grabit' => $this->generateUrl('crm_ticket_user_set', ['id' => $ticket->getId(), 'user_id'=>$user->getId()]),
                ]
            ];
        }
        
        foreach($free as $ticket){
            $tickets['free'][] = [
                'id' => $ticket->getId(),
                'date' => $ticket->getDate()->format('d.m.Y'),
                'username' => null,
                'time' => $ticket->getTime(),
                'task' => $ticket->getTaskTranslate(),
                'status' => $ticket->getStatusTranslate(),
                'links' => [
                    'setstatus' => $this->generateUrl('crm_ticket_status', ['id' => $ticket->getId()]),
                    'show' => $this->generateUrl('crm_ticket_show', ['id' => $ticket->getId()]),
                    'grabit' => $this->generateUrl('crm_ticket_user_set', ['id' => $ticket->getId(), 'user_id'=>$user->getId()]),
                ]
            ];
        }
        
        $data = [
            'tickets' => $tickets
        ];
        return new JsonResponse($data);
    }
    /**
     * @Route("/tickets/user/{id}/future", name="crm_user_future_tickets")
     * @Method("GET")
     */
    public function getUserTicketsFutureAction(User $user){
        $tickets = $this->getDoctrine()->getRepository('AppBundle:Ticket')->getUserFromTodayTikets($user);
        return $this->render('crm/ticket/_list.html.twig',['items'=>$tickets]);
    }
    /**
     * @Route("/tickets/user/{id}/free", name="crm_user_free_tickets")
     * @Method("GET")
     */
    public function getUserTicketsFreeAction(User $user){
        $tickets = $this->getDoctrine()->getRepository('AppBundle:Ticket')->getFreeFromTodayTikets();
        return $this->render('crm/ticket/_list.html.twig',['items'=>$tickets]);
    }
    
    /**
     * @Route("/tickets/new", name="crm_ticket_new")
     */
    public function newAction(Request $request)
    {
        $ticket = new Ticket;
        if($request->get('user')!=""){
//            dump($request->get('user'));
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($request->get('user'));
            $ticket->setUser($user);
        }
        $form = $this->createForm(TicketFormType::class,$ticket);
        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();
            
            $ticket->setCreated(new \DateTime());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            
//            $this->addFlash('success', 'Bid created!');

            if($request->get('submitType')!='apply')
//                return $this->redirectToRoute('crm_ticket_show');
                return $this->render('crm/ticket/show.html.twig', [
//                    'form' => $form->createView(),
                    'ticket' => $ticket
                ]);
            else
                return $this->render('crm/ticket/edit.html.twig', [
                    'form' => $form->createView(),
                    'ticket' => $ticket
                ]);
//                return $this->redirectToRoute('crm_ticket_edit',['id'=>$ticket->getId()]);
        }
//        $data = [
//            'html' => $this->render('crm/ticket/new.html.twig', ['form' => $form->createView(),])
//        ];
//        return new JsonResponse($data);
        return $this->render('crm/ticket/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/tickets/{id}/edit", name="crm_ticket_edit")
     */
    public function editAction(Request $request, Ticket $ticket){

        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($ticket);
            $em->flush();
            
            $this->addFlash('success', 'Ticket updated!');

            if($request->get('submitType')!='apply')
//                return $this->redirectToRoute('crm_ticket_show');
                return $this->render('crm/ticket/edit.html.twig', [
                    'form' => $form->createView(),
                    'item' => $ticket
                ]);
            else
                return $this->render('crm/ticket/show.html.twig', [
//                    'form' => $form->createView(),
                    'ticket' => $ticket
                ]);
           
        }

        return $this->render('crm/ticket/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $ticket
        ]);
    }
    
    /**
     * @Route("/tickets/{id}/status", name="crm_ticket_status")
     */
    public function statusAction(Request $request, Ticket $ticket){
        
        $data = [
            'time' => $ticket->getTime(),
            'date' => $ticket->getDate(),
        ];
        
        $formBuilder = $this->createFormBuilder(null,['data'=>$data]);
        $formBuilder->add('date',DateType::class,[
                    'label' => 'Дата',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker']
                ])
                ->add('time',TextType::class,[
                    'label' => 'Час',
                    'attr' => ['class' => 'js-timepicker']
                ]);
        $form = $formBuilder->getForm();
        
        return $this->render('crm/ticket/cange_status.html.twig', [
            'item' => $ticket,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/tickets/{id}/status/set", name="crm_ticket_status_set")
     */
    public function statusSetAction(Request $request, Ticket $ticket){
      
        $status = $request->get('status');
        $ticket->setStatus($status);
        if($status=='replace'){
            $form = $request->get('form');
            $ticket->setDate(new \DateTime($form['date']));
            $ticket->setTime($form['time']);
        }
        
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();
        
        return $this->render('crm/ticket/cange_status_changed.html.twig', [
            'item' => $ticket,
            'info' => 'Статус змінено'
        ]);
    }
    /**
     * @Route("/tickets/{id}/user/set", name="crm_ticket_user_set")
     */
    public function userSetAction(Request $request, Ticket $ticket){
      
        $user_id = $request->get('user_id');
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($user_id);
        $ticket->setUser($user);
        $em->persist($ticket);
        $em->flush();
        
        $data = [
            'code' => 1,
            'message' => 'Виконавця змінено'
        ];
        return new JsonResponse($data);
//        return $this->render('crm/ticket/cange_status_changed.html.twig', [
//            'item' => $ticket,
//            'info' => 'Виконавця змінено'
//        ]);
    }
    
    /**
     * @Route("/tickets/{id}", name="crm_ticket_show")
     */
    public function showAction(Request $request, Ticket $ticket){
        
        if (!$ticket) {
            throw $this->createNotFoundException('No Ticket found');
        }

        return $this->render('crm/ticket/show.html.twig', array(
            'ticket' => $ticket
        ));
    }
}