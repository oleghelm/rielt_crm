<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ClientRepository extends EntityRepository
{
    public function unlinkClientFromEntitys ($client){
        $this->_em->getRepository('AppBundle:Object')
                ->createQueryBuilder('object')
                ->update()
                ->set('object.client',':nullclient')
                ->where('object.client = :client')
                ->setParameter('client', $client)
                ->setParameter('nullclient', null)
                ->getQuery()
                ->execute();
    }
    public function getClientsForCall ($user){
        $queryBuilder = $this->_em->getRepository('AppBundle:Client')->createQueryBuilder('cl');
        $queryBuilder->orderBy('cl.lastUpdate', 'asc');
        $queryBuilder->andWhere('cl.user = :user')->setParameter('user', $user);
        $date = new \DateTime();
        $date->setDate(date('Y'), date('m'), date('d'));
        $queryBuilder->andWhere('cl.lastUpdate <= :lastUpdate')->setParameter('lastUpdate', $date);
        $query = $queryBuilder->getQuery()->execute();
        return $query;
    }
    
    public function getFilteredClients($filter){
        $queryBuilder = $this->_em->getRepository('AppBundle:Client')->createQueryBuilder('cl');
        $queryBuilder->leftJoin('cl.user', 'user');
        if(isset($filter['type']) && $filter['type'] !=""){
            $queryBuilder->andWhere('cl.type = :type')
                    ->setParameter('type',$filter['type']);
        }
        if(isset($filter['status']) && $filter['status'] !=""){
            $queryBuilder->andWhere('cl.status = :clstatus')
                    ->setParameter('clstatus',$filter['status']);
        } else {
            $queryBuilder->andWhere('cl.status != :clstatus OR cl.status IS NULL')
                    ->setParameter('clstatus','archive');
        }
        if($filter && is_array($filter)){
            if(isset($filter['name']) && $filter['name'] !=""){
                $queryBuilder->andWhere('cl.name LIKE :clname')
                        ->setParameter('clname','%'.$filter['name'].'%');
            }
            if(isset($filter['phones']) && $filter['phones'] !=""){
                $queryBuilder->andWhere('cl.phones LIKE :clphones')
                        ->setParameter('clphones','%'.$filter['phones'].'%');
            }
            if(isset($filter['user'])){
                if(is_array($filter['user']))
                    $queryBuilder->andWhere('cl.user IN '.'('.implode(',',$filter['user']).')');
                elseif($filter['user']!=""){
                    $user = $this->_em->getRepository('AppBundle:User')->find($filter['user']);
                    $queryBuilder->andWhere('cl.user = :user')
                            ->setParameter('user', $user);
                }
            }
            if(isset($filter['lastUpdate'])){
                $queryBuilder->andWhere('cl.lastUpdate <= :lastUpdate')
                        ->setParameter('lastUpdate', $filter['lastUpdate']);
            }            
            if(isset($filter['owner']) && $filter['owner']!=""){
                if($filter['owner']==1)
                    $queryBuilder->andWhere('cl.owner = :owner')
                            ->setParameter('owner', $filter['owner']);
                else
                    $queryBuilder->andWhere('(cl.owner = :owner OR cl.owner IS NULL)')
                            ->setParameter('owner', $filter['owner']);
            }            
        }
        
        $query = $queryBuilder->getQuery();
        
        return $query;
    }
    
    public function createFindAllQuery()
    {
        return $this->_em->getRepository('AppBundle:Client')->createQueryBuilder('cl');
    }
    
    public function findAllClients()
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.status != :status')
            ->setParameter('status', 'archive')
            ->orderBy('user.name', 'ASC');
    }
    public function findAllUserClients($user)
    {
        return $this->createQueryBuilder('cl')
                ->where("cl.user = :user")
                ->setParameter('user', $user)
            ->orderBy('cl.name', 'ASC');
    }

}