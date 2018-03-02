<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Order;
use Doctrine\ORM\EntityRepository;

class BidRepository extends EntityRepository
{
    public function getBidsForCall($user){
        $queryBuilder = $this->_em->getRepository('AppBundle:Bid')->createQueryBuilder('bp');
        $queryBuilder->orderBy('bp.lastUpdate', 'asc');
        $queryBuilder->andWhere('bp.user = :user')->setParameter('user', $user);
        $date = new \DateTime('now -14 days');
        $queryBuilder->andWhere('bp.lastUpdate <= :lastUpdate')->setParameter('lastUpdate', $date);
        $queryBuilder->andWhere('bp.status NOT IN (:status)')->setParameter('status', ['archive','cancel','succesfinish','pause']);
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    public function getFilteredBids($filter){
        $queryBuilder = $this->_em->getRepository('AppBundle:Bid')->createQueryBuilder('bp');
        $queryBuilder->leftJoin('bp.user', 'user');
        $queryBuilder->leftJoin('bp.client', 'client');
        if($filter && is_array($filter)){
            if(isset($filter['name']) && $filter['name']['val'] !=""){
                $queryBuilder->andWhere('bp.name LIKE :obname')
                        ->setParameter('obname','%'.$filter['name']['val'].'%');
            }
            if(isset($filter['user'])){
                if(is_array($filter['user']['val']))
                    $queryBuilder->andWhere('cl.user IN '.'('.implode(',',$filter['user']['val']).')');
                elseif($filter['user']['val']!="")
                    $queryBuilder->andWhere('bp.user = :user')
                            ->setParameter('user', $filter['user']['val']);
            }
            if(isset($filter['client'])){
                if(is_array($filter['client']['val']))
                    $queryBuilder->andWhere('bp.client IN '.'('.implode(',',$filter['client']['val']).')');
                elseif($filter['client']['val']!="")
                    $queryBuilder->andWhere('bp.client = :client')
                            ->setParameter('client', $filter['client']['val']);
            }
            
            if(isset($filter['status'])){
                if(is_array($filter['status']['val']))
                    $queryBuilder->andWhere('bp.status IN '.'(:status)')
                        ->setParameter('status', $filter['status']['val']);
                elseif($filter['status']['val']!="")
                    if($filter['status']['val']=='all'){}
//                        $queryBuilder->andWhere('ob.status = :status')
//                                ->setParameter('status', $filter['status']['val']);
                    else
                    $queryBuilder->andWhere('bp.status = :status')
                            ->setParameter('status', $filter['status']['val']);
            } else {
                    $queryBuilder->andWhere('bp.status != :status')
                            ->setParameter('status', 'archive');
            }
            
            if(isset($filter['type'])){
                if(is_array($filter['type']['val']))
                    $queryBuilder->andWhere('bp.type IN '.'(:type)')
                        ->setParameter('type', $filter['type']['val']);
                elseif($filter['type']['val']!="")
                    $queryBuilder->andWhere('bp.type = :type')
                            ->setParameter('type', $filter['type']['val']);
            }
            
            if(isset($filter['lastUpdateStart']) && $filter['lastUpdateStart']['val']!=""){
                $queryBuilder->andWhere('bp.lastUpdate >= '.'(:lastUpdateStart)')
                    ->setParameter('lastUpdateStart', $filter['lastUpdateStart']['val']);
            }
            if(isset($filter['lastUpdateEnd']) && $filter['lastUpdateEnd']['val']!=""){
                $queryBuilder->andWhere('bp.lastUpdate <= '.'(:lastUpdateEnd)')
                    ->setParameter('lastUpdateEnd', $filter['lastUpdateEnd']['val']);
            }
//            if(isset($filter['min_price'])){
//                $queryBuilder->andWhere('bp.price >= :min_price')
//                        ->setParameter('min_price', $filter['min_price']['val']);
//            }
//            if(isset($filter['max_price'])){
//                $queryBuilder->andWhere('bp.price <= :max_price')
//                        ->setParameter('max_price', $filter['max_price']['val']);
//            }
            
            foreach($filter as $key=>$param){
                if(is_numeric($key)){
                    switch ($param['type']){
                        case 'select':
                            $queryBuilder->leftJoin('bp.params', 'bid_param'.$key);
                            $queryBuilder->andWhere('bid_param'.$key.'.property IN (:param'.$key.')')
                                        ->setParameter('param'.$key, $param['val']);
                            break;
                        case 'text':
                            $queryBuilder->leftJoin('bp.params', 'bid_param'.$key);
                            $queryBuilder->andWhere('bid_param'.$key.'.string LIKE :param'.$key)
                                        ->setParameter('param'.$key, '%'.$param['val'].'%');
                            break;
                        case 'integer':
                            $queryBuilder->leftJoin('bp.params', 'bid_param'.$key);
                            $queryBuilder->andWhere('bid_param'.$key.'.number = :param'.$key)
                                        ->setParameter('param'.$key, $param['val']);
                            break;
                        case 'diapazon':
                            if(isset($param['val']['min']) && isset($param['val']['max'])){
                                $queryBuilder->leftJoin('bp.params', 'bid_param'.$key);
                                $queryBuilder->andWhere('bid_param'.$key.'.number <= :param'.$key.'_max AND bid_param'.$key.'.number >= :param'.$key.'_min')
                                            ->setParameter('param'.$key.'_max', $param['val']['max'])
                                            ->setParameter('param'.$key.'_min', $param['val']['min']);
                            } elseif(isset($param['val']['min']) && !isset($param['val']['max'])){
                                $queryBuilder->leftJoin('bp.params', 'bid_param'.$key);
                                $queryBuilder->andWhere('bid_param'.$key.'.number >= :param'.$key.'_min')
                                            ->setParameter('param'.$key.'_min', $param['val']['min']);
                            } elseif(!isset($param['val']['min']) && isset($param['val']['max'])){
                                $queryBuilder->leftJoin('bp.params', 'bid_param'.$key);
                                $queryBuilder->andWhere('bid_param'.$key.'.number <= :param'.$key.'_max')
                                            ->setParameter('param'.$key.'_max', $param['val']['max']);
                            }
                            break;
                            
                    }
                }
            }
            
            
        } else {
            $queryBuilder->andWhere('bp.status != :status')
                            ->setParameter('status', 'archive');
        }
        
        $query = $queryBuilder->getQuery();
        
        return $query;
    }
    
//    public function getFilteredBids($filter){
//        $queryBuilder = $this->_em->getRepository('AppBundle:Bid')->createQueryBuilder('bp');
//     
//        $query = $queryBuilder->getQuery();
//        
//        return $query;
//    }
}