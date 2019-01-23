<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Ticket;
use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository
{
    public function getUserFromTodayTikets($user){
        $queryBuilder = $this->_em->getRepository('AppBundle:Ticket')->createQueryBuilder('t');
        $queryBuilder->orderBy('t.date', 'asc');
        $queryBuilder->andWhere('t.user = :user')->setParameter('user', $user);
        $date = new \DateTime();
        $date->setDate(date('Y'), date('m'), date('d'));
        $queryBuilder->andWhere('t.date >= :date_from')->setParameter('date_from', $date);
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    public function getFreeFromTodayTikets(){
        $queryBuilder = $this->_em->getRepository('AppBundle:Ticket')->createQueryBuilder('t');
        $queryBuilder->orderBy('t.date', 'asc');
        $queryBuilder->andWhere('t.user IS NULL');
        $date = new \DateTime();
        $date->setDate(date('Y'), date('m'), date('d'));
        $queryBuilder->andWhere('t.date >= :date_from')->setParameter('date_from', $date);
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    
    
    public function getFilteredTickets($filter){
        $queryBuilder = $this->_em->getRepository('AppBundle:Ticket')->createQueryBuilder('t');
        $queryBuilder->orderBy('t.date', 'asc');
        
        if($filter && is_array($filter)){
            if(isset($filter['user'])){
//                if(is_array($filter['user']))
//                    $queryBuilder->andWhere('t.user IN '.'('.implode(',',$filter['user']).')');
//                elseif($filter['user']!="")
                    $queryBuilder->andWhere('t.user = :user')
                            ->setParameter('user', $filter['user']);
            }
            if(isset($filter['date_from'])){
                $queryBuilder->andWhere('t.date >= :date_from')
                        ->setParameter('date_from', $filter['date_from']);
            }            
            if(isset($filter['date_to'])){
                $queryBuilder->andWhere('t.date <= :date_to')
                        ->setParameter('date_to', $filter['date_to']);
            }            
            if(isset($filter['task'])){
                $queryBuilder->andWhere('t.task = :task')
                        ->setParameter('task', $filter['task']);
            }            
        } else {
            $date = new \DateTime();
            $date->setDate(date('Y'), date('m'), 1);
            $queryBuilder->andWhere('t.date >= :date_from')
                    ->setParameter('date_from', $date);
        }
        
        
        $query = $queryBuilder->getQuery();
        
        return $query;
    }
    public function getFilteredUserTickets($user, $filter, $execute = false){
        $queryBuilder = $this->_em->getRepository('AppBundle:Ticket')->createQueryBuilder('t');
        $queryBuilder->orderBy('t.date', 'asc');
        $queryBuilder->andWhere('t.user = :user')
                ->setParameter('user', $user);

        if($filter && is_array($filter)){
            if(isset($filter['date_type']) && $filter['date_type']=='day'){
                if(isset($filter['date_current'])){
                    $queryBuilder->andWhere('t.date = :date_current')
                            ->setParameter('date_current', $filter['date_current']);
                }            
            } else {
                if(isset($filter['date_from'])){
                    $queryBuilder->andWhere('t.date >= :date_from')
                            ->setParameter('date_from', $filter['date_from']);
                }            
                if(isset($filter['date_to'])){
                    $queryBuilder->andWhere('t.date <= :date_to')
                            ->setParameter('date_to', $filter['date_to']);
                }            
            }
            if(isset($filter['task'])){
                $queryBuilder->andWhere('t.task = :task')
                        ->setParameter('task', $filter['task']);
            }
            if(isset($filter['status'])){
                if(is_array($filter['status'])){
                    $queryBuilder->andWhere('t.status IN (:status)')
                        ->setParameter('status', $filter['status']);
                } else
                    $queryBuilder->andWhere('t.status = :status')
                        ->setParameter('status', $filter['status']);
            }
        } else {
            $date = new \DateTime();
            $date->setDate(date('Y'), date('m'), 1);
            $queryBuilder->andWhere('t.date >= :date_from')
                    ->setParameter('date_from', $date);
        }
        
        $query = $queryBuilder->getQuery();
        
        if($execute)
            $query = $query->execute();
        
        return $query;
    }
    
}