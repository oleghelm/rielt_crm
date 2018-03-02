<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="object_param")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObjectParamRepository")
 */
class ObjectParam
{    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Object", inversedBy="params")
     * @ORM\JoinColumn(nullable=true)
     */
    private $object;
    
    /**
     * @ORM\ManyToOne(targetEntity="Param", inversedBy="objectParams")
     * @ORM\JoinColumn(nullable=true)
     */
    private $param;
    
    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="objectParams")
     * @ORM\JoinColumn(nullable=true)
     */
    private $property;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $string;
        
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number;
        
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $floatnumber;
    
    function getId() {
        return $this->id;
    }

    function getObject() {
        return $this->object;
    }

    /**
     * @return Param
     */
    function getParam() {
        return $this->param;
    }

    /**
     * @return Property
     */
    function getProperty() {
        return $this->property;
    }

    function getString() {
        return $this->string;
    }

    function getNumber() {
        return $this->number;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setObject($object) {
        $this->object = $object;
    }

    function setParam($param) {
        $this->param = $param;
    }

    function setProperty($property) {
        $this->property = $property;
    }

    function setString($string) {
        $this->string = $string;
    }

    function setNumber($number) {
        $this->number = $number;
    }

    function getFloatnumber() {
        return $this->floatnumber;
    }

    function setFloatnumber($floatnumber) {
        $this->floatnumber = $floatnumber;
    }

}