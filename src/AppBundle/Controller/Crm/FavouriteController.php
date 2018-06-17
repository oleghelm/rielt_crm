<?php

namespace AppBundle\Controller\Crm;

use AppBundle\Entity\Favourite;
use AppBundle\Entity\Bid;
use AppBundle\Entity\User;
use AppBundle\Entity\Object;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 * @Route("/crm")
 */
class FavouriteController extends Controller
{
    /**
     * @Route("/favourite/add/{id}", name="crm_favourite_add")
     */
    public function addAction(Request $request, Object $object)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();
        if(!$em->getRepository('AppBundle:Favourite')->checkIfFavourite($object,$user)){
            $favourite = new Favourite;
            $favourite->setUser($user);
            $favourite->setObject($object);
            $em = $this->getDoctrine()->getManager();
            $em->persist($favourite);
            $em->flush();
            $data = ['status' => '1'];
        } else {
            $vals = $em->getRepository('AppBundle:Favourite')->getFavourite($object,$user);
            foreach ($vals as $val){
                $em->remove($val);
                $em->flush();
            }
            $data = ['status' => '0'];
        }
        return new JsonResponse($data);
    }
    
    /**
     * @Route("/favourite/addtobid/{id}", name="crm_favourite_add_to_bid")
     */
    public function addToBidAction(Request $request, Object $object)
    {
        $html = '';
        if(!$request->get('bid',false)){
            //if no bid,return bids selector
            $em = $this->getDoctrine()->getEntityManager();
            $bids = $em->getRepository('AppBundle:Bid')->getBids();
            $html .= '<label for="check_bid_select">Виберіть заявку</label>';
            $html .= '<select name="check_bid" id="check_bid_select">';
            $html .= '<option value="">Оберіть заявку</option>';
            foreach($bids as $bid){
                $html .= '<option value="'.$bid->getId().'">'.$bid->getId().' '.$bid->getName().'</option>';
            }
            $html .= '</select><br><br>';
            $html .= '<a href="#PATH#" class="btn btn-success pull-right favourite-add-to-bid">Додати до заявки</a>';
        } else {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $em = $this->getDoctrine()->getEntityManager();
            $bid = $em->getRepository('AppBundle:Bid')->find($request->get('bid',false));
            if(!$em->getRepository('AppBundle:Favourite')->checkIfFavourite($object,$user,$bid)){
                $favourite = new Favourite;
                $favourite->setUser($user);
                $favourite->setObject($object);
                $favourite->setBid($bid);
                $em = $this->getDoctrine()->getManager();
                $em->persist($favourite);
                $em->flush();
            }
            $html .= '<h3>Додано</h3>';
        }
        $data = [
            'status' => '0',
            'html' => $html
        ];
        return new JsonResponse($data);
    }
     /**
     * @Route("/favourite/delfrombid", name="crm_favourite_del_from_bid")
     */
    public function delFromBidAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $bid = $em->getRepository('AppBundle:Bid')->find($request->get('bid',false));
        $object = $em->getRepository('AppBundle:Object')->find($request->get('id',false));
        $favourites = $em->getRepository('AppBundle:Favourite')->getFavouriteObjectInBid($object,$bid);
        foreach($favourites as $favourite){
            $em->remove($favourite);
            $em->flush();
        }
        $data = [
            'status' => '1',
        ];
        return new JsonResponse($data);
    }
}