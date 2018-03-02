<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Location;
use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository
{
    public function findAllLocations()
    {
        return $this->createQueryBuilder('location')
            ->orderBy('location.name', 'ASC');
    }
    public function findAllCityLocations()
    {
        return $this->createQueryBuilder('location')
            ->andWhere('location.level = :level')
            ->setParameter('level', 4)
            ->orderBy('location.name', 'ASC');
    }
    public function getLocationsForFilter($levels = array(4)){
        $vals = $this->createQueryBuilder('location')
            ->orderBy('location.name', 'ASC')
            ->andWhere('location.level IN (:levels)')
            ->setParameter('levels', $levels)
            ->getQuery()
            ->execute();
        $arr = [];
        foreach($vals as $val){
            $arr[$val->getName()] = $val->getId();
        }
        return $arr;
    }
}