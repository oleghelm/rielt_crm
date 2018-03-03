<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="param")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ParamRepository")
 */
class Param
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $useInFilter = false;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $filter = [];
    
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $detail = [];
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $multiple;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    private $type;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $sort;
        
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $useInExport = false;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $basicParam = false;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $exportName;
    
    /**
     * @ORM\OneToMany(targetEntity="Property", mappedBy="param", orphanRemoval=true)
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $properties;
    
    /**
     * @ORM\OneToMany(targetEntity="ObjectParam", mappedBy="param", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $objectParams;
    
    /**
     * @ORM\OneToMany(targetEntity="BidParam", mappedBy="param", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $bidParams;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $section;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getUseInFilter() {
        return $this->useInFilter;
    }

    function getType() {
        return $this->type;
    }

    function getSort() {
        return $this->sort;
    }

    /**
     * @return ArrayCollection|Property[]
     */
    function getProperties() {
        return $this->properties;
    }

    /**
     * @return ArrayCollection|ObjectParam[]
     */
    function getObjectParams() {
        return $this->objectParams;
    }

    /**
     * @return ArrayCollection|ObjectParam[]
     */
    function getBidParams() {
        return $this->bidParams;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setUseInFilter($useInFilter) {
        $this->useInFilter = $useInFilter;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setSort($sort) {
        $this->sort = $sort;
    }

    function setProperties($properties) {
        $this->properties = $properties;
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    function getMultiple() {
        return $this->multiple;
    }

    function setMultiple($multiple) {
        $this->multiple = $multiple;
    }
    
    function getFilter() {
        return $this->filter;
    }

    function getDetail() {
        return $this->detail;
    }

    function setFilter($filter) {
        $this->filter = $filter;
    }

    function setDetail($detail) {
        $this->detail = $detail;
    }
    
    function setObjectParams($objectParams) {
        $this->objectParams = $objectParams;
    }

    function setBidParams($bidParams) {
        $this->bidParams = $bidParams;
    }
    
    function getUseInExport() {
        return $this->useInExport;
    }

    function getBasicParam() {
        return $this->basicParam;
    }

    function getExportName() {
        return $this->exportName;
    }

    function setUseInExport($useInExport) {
        $this->useInExport = $useInExport;
    }

    function setBasicParam($basicParam) {
        $this->basicParam = $basicParam;
    }

    function setExportName($exportName) {
        $this->exportName = $exportName;
    }
    
    function getSection() {
        return $this->section;
    }

    function setSection($section) {
        $this->section = $section;
    }    
}