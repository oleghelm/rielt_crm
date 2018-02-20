<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Property;
use Doctrine\ORM\EntityRepository;

class PropertyRepository extends EntityRepository
{
    public function findAllProperties()
    {
        $queryBuilder = $this->_em->getRepository('AppBundle:Property')->createQueryBuilder('p');
        
        $query = $queryBuilder->getQuery();
        
        return $query;
    }

}