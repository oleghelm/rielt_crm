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
    public function findPropertyByExportName($param,$ename)
    {
        $queryBuilder = $this->_em->getRepository('AppBundle:Property')->createQueryBuilder('p');
        $queryBuilder->andWhere('( p.exportName = :ename OR p.name = :ename )')->setParameter('ename', $ename);
        $queryBuilder->andWhere('p.param = :param')->setParameter('param', $param);
        $query = $queryBuilder->getQuery()->getResult();
        if(!empty($query))
            return $query[0];
        else return NULL;
    }

}