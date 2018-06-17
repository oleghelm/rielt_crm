<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class FavouriteRepository extends EntityRepository
{
    public function checkIfFavourite($object,$user,$bid = false){
        $queryBuilder = $this->_em->getRepository('AppBundle:Favourite')->createQueryBuilder('fv');
        $queryBuilder->andWhere('fv.object = :object')->setParameter('object',$object);
        if($bid){
            $queryBuilder->andWhere('fv.bid = :bid')->setParameter('bid',$bid);
        } else {
            $queryBuilder->andWhere('fv.user = :user')->setParameter('user',$user);
        }
        $vals = $queryBuilder->getQuery()->execute();
        $arr = [];
        foreach($vals as $val){
            $arr[] = ['id' => $val->getId(), 'user' => $val->getUser(), 'object' => $val->getObject(), 'bid' => $val->getBid()];
        }
        return (count($arr)>0);
    }
    
    public function getFavourite($object,$user,$bid = false){
        $queryBuilder = $this->_em->getRepository('AppBundle:Favourite')->createQueryBuilder('fv');
        $queryBuilder->andWhere('fv.object = :object')->setParameter('object',$object);
        if($bid){
            $queryBuilder->andWhere('fv.bid = :bid')->setParameter('bid',$bid);
        } else {
            $queryBuilder->andWhere('fv.user = :user')->setParameter('user',$user);
        }
        $vals = $queryBuilder->getQuery()->execute();
        $arr = [];
        foreach($vals as $val){
            $arr[] = $val;
        }
        return $arr;
    }
    
    public function getFavouriteObjectInBid($object, $bid){
        $queryBuilder = $this->_em->getRepository('AppBundle:Favourite')->createQueryBuilder('fv');
        $queryBuilder->andWhere('fv.object = :object')->setParameter('object',$object);
        $queryBuilder->andWhere('fv.bid = :bid')->setParameter('bid',$bid);
        $vals = $queryBuilder->getQuery()->execute();
        $arr = [];
        foreach($vals as $val){
            $arr[] = $val;
        }
        return $arr;
    }
    
    
    public function getFiltered($filter){
        $queryBuilder = $this->_em->getRepository('AppBundle:Favourite')->createQueryBuilder('ag');
        $queryBuilder->leftJoin('ag.user', 'user');
        if(isset($filter['type']) && $filter['type'] !=""){
            $queryBuilder->andWhere('ag.type = :type')
                    ->setParameter('type',$filter['type']);
        }
        if(isset($filter['status']) && $filter['status'] !=""){
            $queryBuilder->andWhere('ag.status = :status')->setParameter('status',$filter['status']);
        } else {
            $queryBuilder->andWhere('ag.status != :status')->setParameter('status','archive');
        }

        if($filter && is_array($filter)){
            if(isset($filter['name']) && $filter['name'] !=""){
                $queryBuilder->andWhere('ag.name LIKE :clname')
                        ->setParameter('clname','%'.$filter['name'].'%');
            }
            if(isset($filter['user'])){
                if(is_array($filter['user']))
                    $queryBuilder->andWhere('ag.user IN '.'('.implode(',',$filter['user']).')');
                elseif($filter['user']!=""){
                    $user = $this->_em->getRepository('AppBundle:User')->find($filter['user']);
                    $queryBuilder->andWhere('ag.user = :user')
                            ->setParameter('user', $user);
                }
            }          
            if(isset($filter['client'])){
                if(is_array($filter['client']))
                    $queryBuilder->andWhere('ag.client IN '.'('.implode(',',$filter['client']).')');
                elseif($filter['client']!=""){
                    $client = $this->_em->getRepository('AppBundle:Client')->find($filter['client']);
                    $queryBuilder->andWhere('ag.client = :client')
                            ->setParameter('client', $client);
                }
            }          
        }
        
        $query = $queryBuilder->getQuery();
        
        return $query;
    }
}