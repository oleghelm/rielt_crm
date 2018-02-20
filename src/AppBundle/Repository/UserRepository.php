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
    }
    public function getUsersForFilter(){
        $vals = $this->createQueryBuilder('user')
            ->orderBy('user.name', 'ASC')
            ->getQuery()
            ->execute();
        $arr = [];
        foreach($vals as $val){
            $arr[$val->getName()] = $val->getId();
//            dump($val);
        }
        return $arr;
    }
}