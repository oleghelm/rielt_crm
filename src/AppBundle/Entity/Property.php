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
     * @ORM\Column(type="integer")
     */
    private $sort;
    
    /**
     * @ORM\ManyToOne(targetEntity="Param", inversedBy="properties")
     * @ORM\JoinColumn(nullable=true)
     */
    private $param;
    
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