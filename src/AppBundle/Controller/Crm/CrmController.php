<?php

namespace AppBundle\Controller\Crm;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Ticket;
use AppBundle\Entity\User;
use AppBundle\Entity\Bid;

/**
 * @Security("is_granted('ROLE_CRM_USER') or is_granted('ROLE_SUPERADMIN')")
 */
class CrmController extends Controller
{
    /**
     * @Route("/crm", name="crm_index")
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $statistic = $this->getBasicStatistic($user);
        $statistic['personal'] = $this->getDoctrine()->getRepository('AppBundle:User')->userStatisticNumbers($user);
//        dump($total);
        
        $importantObjects = $this->getDoctrine()->getRepository('AppBundle:Object')->getImportant();
        return $this->render('crm/index.html.twig',[
            'statistic' => $statistic,
            'importantObjects' => $importantObjects,
        ]);
    }
    
    /**
     * @Route("/crm/object_statistic", name="crm_object_statistic")
     */
    public function objectStatisticAction()
    {
        $statistic = $this->getBasicStatistic();
        return $this->render('crm/statistic/object_statistic.html.twig',[
            'statistic' => $statistic['statistic'],
            'users' => $statistic['users'],
            'companies' => $statistic['companies']
        ]);
    }
      
    /**
     * @Route("/crm/get_fav_count", name="crm_get_fav_count")
     */
    public function getFavouritesCount(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $count = $this->getDoctrine()->getRepository('AppBundle:User')->getUserFavouritesCount($user);
        return $this->render('crm/tools/fav_count.html.twig',[
            'count' => $count,
        ]);
    }
    
    public function getBasicStatistic($user = false){
        if($user)
            $total = $this->getDoctrine()->getRepository('AppBundle:Object')->getStatistcByUser($user);
        else
            $total = $this->getDoctrine()->getRepository('AppBundle:Object')->getStatistcByCompanies();
        //make work array with basic data
        $res = [];
        $users = [];
        $companies = [];
        foreach ($total as $object){
            $company_id = false;
            $user_id = false;
            if($object->getCompany()){
                $company_id = $object->getCompany()->getId();
                if(!isset($companies[$company_id])){
                    $companies[$company_id] = $object->getCompany();
                }
            } else {continue;}
            if($object->getUser()){
                $user_id = $object->getUser()->getId();
                if(!isset($users[$user_id])){
                    $users[$user_id] = $object->getUser();
                }
            } else {continue;}
            $res[] = [
                'company_id' => $company_id,
                'object_id' => $object->getId(),
                'created' => $object->getCreated(),
                'user_id' => $object->getUser()->getId(),
                'created_by' => $object->getCreatedBy()->getId(),
                'status' => $object->getStatus()
            ];
        }
        
        //count statistic creating by last year by months
        $yearly_companies_by_month = [];
        //count statistic creating by each user by month
        $yearly_users_by_month = [];
        //count selling by users by month
        $yearly_users_selling_by_month = [];
        foreach ($res as $object){
            $month = (int)$object['created']->format('m');
            $company_id = $object['company_id'];
            $user_id = $object['user_id'];
            $created_by = $object['created_by'];
            if(!isset($yearly_companies_by_month[$company_id])) $yearly_companies_by_month[$company_id]['items'] = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
            $yearly_companies_by_month[$company_id]['items'][$month]++;
            
            if(!isset($yearly_users_by_month[$created_by])) $yearly_users_by_month[$created_by]['items'] = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
            $yearly_users_by_month[$created_by]['items'][$month]++;
            
            if($object['status']=='saled'){
                if(!isset($yearly_users_selling_by_month[$user_id])) $yearly_users_selling_by_month[$user_id]['items'] = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0];
                $yearly_users_selling_by_month[$user_id]['items'][$month]++;
            }
        }
        $colors = ['#93b8c9','#98b280','#cd9b9b','#c17b74','#776977','#647b60','#e1ffc5','#98b280','#c17b74','#767ba5','#a1759c','#ffd600','#d400e2','#2c3571','#9eb6b8','#741a1a','#d4701c','#5ac739'];
        foreach ($yearly_companies_by_month as $i=>$yearly_companies_by_month_){
//            ksort($yearly_companies_by_month[$i]);
            $yearly_companies_by_month[$i]['color'] = next($colors);
        }
        reset($colors);
        foreach ($yearly_users_by_month as $i=>$yearly_users_by_month_){
            $yearly_users_by_month[$i]['color'] = next($colors);
        }
        reset($colors);
        foreach ($yearly_users_selling_by_month as $i=>$yearly_users_selling_by_month_){
            $yearly_users_selling_by_month[$i]['color'] = next($colors);
        }
        
        
//        dump($yearly_companies_by_month);
//        dump($yearly_users_by_month);
        $statistic = [
            'yearly_companies_by_month' => $yearly_companies_by_month,
            'yearly_users_by_month' => $yearly_users_by_month,
            'yearly_users_selling_by_month' => $yearly_users_selling_by_month
        ];
        
        return [
            'statistic' => $statistic,
            'users' => $users,
            'companies' => $companies
        ];
    }
  
}