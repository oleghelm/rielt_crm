<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PropertyRepository")
 */
class Property
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
     * @Assert\NotBlank()
     * @ORM\Column(type="string", nullable=true)
     */
    private $exportName;
   
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer")
     */
    private $sort;
    
    /**
     * @ORM\ManyToOne(targetEntity="Param", inversedBy="properties")
     * @ORM\JoinColumn(nullable=true)
     */
    private $param;
    
    /**
     * @ORM\OneToMany(targetEntity="ObjectParam", mappedBy="property", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $objectParams;
    
    /**
     * @ORM\OneToMany(targetEntity="BidParam", mappedBy="property", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $bidParams;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getSort() {
        return $this->sort;
    }

    function getParam() {
        return $this->param;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setSort($sort) {
        $this->sort = $sort;
    }

    function setParam($param) {
        $this->param = $param;
    }
    function getExportName() {
        return $this->exportName;
    }

    function setExportName($exportName) {
        $this->exportName = $exportName;
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function delete(){
        //call removing of other
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($this);
        $em->flush();
    }
}