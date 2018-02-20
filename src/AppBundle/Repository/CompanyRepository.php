<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Company;
use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
    public function findAllCompanies()
    {
        return $this->createQueryBuilder('company')
            ->orderBy('company.name', 'ASC');
    }
    public function getForFilter(){
        $vals = $this->createQueryBuilder('company')
            ->orderBy('company.name', 'ASC')
            ->getQuery()
            ->execute();
        $arr = [];
        $attrArr = [];
        foreach($vals as $val){
            $arr[$val->getName()] = $val->getId();
//            $attrArr[$val->getName()] = ['style' => 'background:url(/images/company/'.$val->getPreview().') no-repeat center center cover;'];
//            $arr[htmlspecialchars_decode('<img src="/images/company/'.$val->getPreview().'">')] = $val->getId();
        }
        return $arr;
    }
    public function getImagesForFilter(){
        $vals = $this->createQueryBuilder('company')
            ->orderBy('company.name', 'ASC')
            ->getQuery()
            ->execute();
        $arr = [];
        $attrArr = [];
        foreach($vals as $val){
//            $arr[$val->getName()] = $val->getId();
//            $attrArr[$val->getName()] = ['style' => 'background:url(/images/company/'.$val->getPreview().') no-repeat center center cover;'];
            $attrArr[$val->getName()] = ['data-image' => '/images/company/'.$val->getPreview()];
//            $arr[htmlspecialchars_decode('<img src="/images/company/'.$val->getPreview().'">')] = $val->getId();
        }
        return $attrArr;
    }
}