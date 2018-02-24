<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="bid_param")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BidParamRepository")
 */
class BidParam
{    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Bid", inversedBy="params")
     * @ORM\JoinColumn(nullable=true)
     */
    private $bid;
    
    /**
     * @ORM\ManyToOne(targetEntity="Param", inversedBy="bidParams")
     * @ORM\JoinColumn(nullable=true)
     */
    private $param;
    
    /**
     * @ORM\ManyToOne(targetEntity="Property", inversedBy="bidParams")
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
    
    function getId() {
        return $this->id;
    }

    function getBid() {
        return $this->bid;
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

    function setBid($bid) {
        $this->bid = $bid;
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


}