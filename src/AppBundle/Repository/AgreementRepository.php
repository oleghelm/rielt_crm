<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class AgreementRepository extends EntityRepository
{
    
    public function getFiltered($filter){
        $queryBuilder = $this->_em->getRepository('AppBundle:Agreement')->createQueryBuilder('ag');
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