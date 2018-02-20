<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="bid")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BidRepository")
 */
class Bid
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $important = false;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $info;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bids")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="bids")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastUpdate;
        
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_price;
        
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_price;
       
    /**
     * @ORM\OneToMany(targetEntity="BidParam", mappedBy="bid", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $params;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getStatus() {
        return $this->status;
    }

    function getType() {
        return $this->type;
    }

    function getImportant() {
        return $this->important;
    }

    function getInfo() {
        return $this->info;
    }
    
    /**
     * @return ArrayCollection|GenusNote[]
     */
    function getUser() {
        return $this->user;
    }

    /**
     * @return ArrayCollection|GenusNote[]
     */
    function getClient() {
        return $this->client;
    }

    function getLastUpdate() {
        return $this->lastUpdate;
    }

    function getMinPrice() {
        return $this->min_price;
    }

    function getMaxPrice() {
        return $this->max_price;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setImportant($important) {
        $this->important = $important;
    }

    function setInfo($info) {
        $this->info = $info;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setClient($client) {
        $this->client = $client;
    }

    function setLastUpdate($lastUpdate) {
        $this->lastUpdate = $lastUpdate;
    }

    function setMinPrice($min_price) {
        $this->min_price = $min_price;
    }

    function setMaxPrice($max_price) {
        $this->max_price = $max_price;
    }

    public function __toString()
    {
        return $this->getName();
    }
    
    
    /**
     * @return ArrayCollection|ObjectParam[]
     */
    function getParams() {
        return $this->params;
    }
    
    public function getParamsArrayMap($joinMultiple = false){
        $allparams = $this->getParams();
        $Params = [];
        if($allparams)
        foreach($allparams as $p){
            $param = $p->getParam();
            $Param = [];
            $Param['id'] = $p->getId();
            $Param['param_id'] = $param->getId();
            $Param['type'] = $param->getType();
            $Param['multiple'] = $param->getMultiple();
            if($Param['type']=='integer' || $Param['type']=='diapazon')
                $Param['val'] = $p->getNumber();
            elseif($Param['type']=='text')
                $Param['val'] = $p->getString();
            else{
                $Param['multiple'] = true;
                $Param['val'] = $p->getProperty()->getId();
            }
            
            $Params[$p->getId()] = $Param;
        }
        if($joinMultiple){
            $formParams = [];
            foreach ($Params as $p){
                if($p['multiple'])
                    $formParams[$p['param_id']][] = $p['val'];
                else
                    $formParams[$p['param_id']] = $p['val'];
            }
            $Params = $formParams;
        }
        return $Params;
    }
}