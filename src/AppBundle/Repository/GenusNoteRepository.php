<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Genus;

class GenusNoteRepository extends EntityRepository
{
    /**
     * @param Genus $genus
     * @return GenusNote[]
     */
    public function findAllRecentNotesForGenus(Genus $genus)
    {
        return $this->createQueryBuilder('genus_note')
                ->andWhere('genus_note.genus = :genus')
                ->setParameter('genus', $genus)
                ->andWhere('genus_note.createdAt > :recentDate')
                ->setParameter('recentDate', new \DateTime('-3 month'))
                ->getQuery()
                ->execute();
    }
}