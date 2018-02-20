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
     * @ORM\OneToMany(targetEntity="Property", mappedBy="param", orphanRemoval=true)
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $properties;
    
    /**
     * @ORM\OneToMany(targetEntity="ObjectParam", mappedBy="param", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $objectParams;
    
    
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

}