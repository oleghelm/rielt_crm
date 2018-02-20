<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BidParam;
use Doctrine\ORM\EntityRepository;

class BidParamRepository extends EntityRepository
{
        public function findAllForBid()
    {
        /**
         * @return Param[]
         */
        return $this->createQueryBuilder('bid_param')
//            ->andWhere('param.useInFilter = :useInFilter')
//            ->setParameter('useInFilter', true)
            ->orderBy('bid_param.sort', 'ASC')
            ->getQuery()
            ->execute();
    }
}