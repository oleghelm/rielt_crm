<?php

namespace AppBundle\Repository;

use AppBundle\Entity\ObjectParam;
use Doctrine\ORM\EntityRepository;

class ObjectParamRepository extends EntityRepository
{
        public function findAllForObject()
    {
        /**
         * @return Param[]
         */
        return $this->createQueryBuilder('object_param')
//            ->andWhere('param.useInFilter = :useInFilter')
//            ->setParameter('useInFilter', true)
            ->orderBy('param.sort', 'ASC')
            ->getQuery()
            ->execute();
    }
}