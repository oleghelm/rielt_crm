<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Param;
use Doctrine\ORM\EntityRepository;

class ParamRepository extends EntityRepository
{
    public function findAllParams()
    {
        $queryBuilder = $this->_em->getRepository('AppBundle:Param')->createQueryBuilder('p');
        
        $query = $queryBuilder->getQuery();
        
        return $queryBuilder;
    }
    
    public function findAllParamsForProperties()
    {
        return $this->createQueryBuilder('param')
            ->andWhere('param.type IN (:types)')
            ->setParameter('types', ["select","select_multiple"])
            ->orderBy('param.name', 'ASC');
    }
    
    /**
     * @return Param[]
     */
    public function findAllParamsForForm($filterType = '')
    {
        $builder = $this->createQueryBuilder('param')
//            ->andWhere('param.useInFilter = :useInFilter')
//            ->setParameter('useInFilter', true)
            ->orderBy('param.sort', 'ASC')
            ->getQuery()
            ->execute();
//        if($filterType!=""){
//            $builder ->andWhere('param.filter = :filtertype')
//            ->setParameter('filtertype', $filterType);
//        }
        return $builder;
    }
    /**
     * @return Param[]
     */
    public function findAllParamsForFilterForm($filterType = '')
    {
        if($filterType!=""){
            $builder = $this->createQueryBuilder('param')
                ->andWhere('param.useInFilter = :useInFilter')
                ->setParameter('useInFilter', true)
                ->andWhere('param.filter LIKE :filtertype')
                ->setParameter('filtertype', '%'.$filterType.'%')
                ->orderBy('param.sort', 'ASC')
                ->getQuery()
                ->execute();
        } else {
            $builder = $this->createQueryBuilder('param')
                ->andWhere('param.useInFilter = :useInFilter')
                ->setParameter('useInFilter', true)
                ->orderBy('param.sort', 'ASC')
                ->getQuery()
                ->execute();
            
        }
        return $builder;
    }
    
}