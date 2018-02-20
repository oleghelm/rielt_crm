<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GenusRepository extends EntityRepository
{
    public function findAllPublishedOrderedByRecentlyActive()
    {
        /**
         * @return Genus[]
         */
        return $this->createQueryBuilder('genus')
                ->andWhere('genus.isPublished = :isPublished')
                ->setParameter('isPublished', true)
                ->leftJoin('genus.notes', 'genus_note')
                
//                ->leftJoin('genus.subFamily', 'sub_family')
//                ->andWhere('sub_family.name = :subFamily')
//                ->setParameter('subFamily', 'jnienow')
                
                ->orderBy('genus_note.createdAt', 'DESC')
                ->getQuery()
                ->execute();
    }
}