<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Domriaid;
use AppBundle\Entity\Favourite;
use Doctrine\ORM\EntityRepository;

class DomriaidRepository extends EntityRepository
{
    public function getObjectsByDomriaIds($ids){
        $queryBuilder = $this->createQueryBuilder('domriaid');
        $queryBuilder->where('domriaid.did IN (:ids)')->setParameter('ids', $ids);
        return $queryBuilder->getQuery()->execute();
    }
}