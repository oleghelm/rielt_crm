<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllUsers()
    {
        return $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC');
    }
    
    public function findAllUsersByIds($ids)
    {
        return $this->createQueryBuilder('user')
            ->where('user.id IN :ids')
            ->setParameter('ids', '('.implode(',',$ids).')')
            ->orderBy('user.name', 'ASC');
    }
    public function unlinkUserFromEntitys ($user){
        $this->_em->getRepository('AppBundle:Client')
                ->createQueryBuilder('client')
                ->update()
                ->set('client.user',':nulluser')
                ->where('client.user = :user')
                ->setParameter('user', $user)
                ->setParameter('nulluser', null)
                ->getQuery()
                ->execute();
        $this->_em->getRepository('AppBundle:Object')
                ->createQueryBuilder('object')
                ->update()
                ->set('object.user',':nulluser')
                ->where('object.user = :user')
                ->setParameter('user', $user)
                ->setParameter('nulluser', null)
                ->getQuery()
                ->execute();
        $this->_em->getRepository('AppBundle:Bid')
                ->createQueryBuilder('bid')
                ->update()
                ->set('bid.user',':nulluser')
                ->where('bid.user = :user')
                ->setParameter('user', $user)
                ->setParameter('nulluser', null)
                ->getQuery()
                ->execute();
        $this->_em->getRepository('AppBundle:Ticket')
                ->createQueryBuilder('ticket')
                ->delete()
                ->where('ticket.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->execute();
    }
    public function getUsersForFilter(){
        $vals = $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC')
            ->getQuery()
            ->execute();
        $arr = [];
        foreach($vals as $val){
            $arr[$val->getName()] = $val->getId();
        }
        return $arr;
    }
    public function userStatisticNumbers($user){
        $result = [];
        
        $repository = $this->_em->getRepository('AppBundle:Object')->createQueryBuilder('o');
        
        $qb = $repository->select('count(o.id)')
            ->where('o.created_by = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        $result['count_created'] = $qb;

        $qb = $repository->select('count(o.id)')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        $result['count_in_use'] = $qb;
        
        $qb = $repository->select('count(o.id) as num, o.status')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->groupBy('o.status')
            ->getQuery()
            ->getArrayResult();
        $result['count_in_use_by_statys'] = $qb;
        
        $repository = $this->_em->getRepository('AppBundle:Client')->createQueryBuilder('cl');
        
        $qb = $repository->select('count(cl.id)')
            ->where('cl.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        $result['count_clients'] = $qb;
        
        
        
        return $result;
    }
    
    public function getUserFavouritesCount($user){
        $repository = $this->_em->getRepository('AppBundle:Favourite')->createQueryBuilder('f');
        $qb = $repository->select('count(f.id)')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
        return $qb;
    }
}