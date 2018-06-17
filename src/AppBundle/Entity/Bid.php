<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Location;
use AppBundle\Repository\LocationRepository;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $baseprice;
        
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $min_price;
        
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_price;
           
    /**
     * @ORM\Column(type="json_array")
     */
    private $location;
    
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $rooms;
    
    /**
     * @ORM\OneToMany(targetEntity="BidParam", mappedBy="bid", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $params;
    
    /**
     * @ORM\OneToMany(targetEntity="Favourite", mappedBy="bid", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $favourites;
    private $objects = [];
    
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
    /**
     * @return ArrayCollection|int[]
     */
    function getLocation() {
        return $this->location;
    }

    function setLocation($location) {
        $this->location = $location;
    }
    
    function getBaseprice() {
        return $this->baseprice;
    }

    function setBaseprice($baseprice) {
        $this->baseprice = $baseprice;
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    /**
     * @return ArrayCollection|int[]
     */
    function getRooms() {
        return $this->rooms;
    }

    function setRooms($rooms) {
        $this->rooms = $rooms;
    }

    /**
     * @return ArrayCollection|ObjectParam[]
     */
    function getParams() {
        return $this->params;
    }
    
    /**
     * @return ArrayCollection|Favourite[]
     */
    function getFavourites() {
        return $this->favourites;
    }

    function setFavourites($favourites) {
        $this->favourites = $favourites;
    }
    
    /**
     * @return ArrayCollection|Favourite[]
     */
    function getObjects() {
        return $this->objects;
    }

    function setObjects($objects) {
        $this->objects = $objects;
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
            if($Param['type']=='integer' || $Param['type']=='diapazon'){
                $Param['multiple'] = true;
                $Param['type'] = 'diapazon';
                $Param['val'] = $p->getNumber();
            }
            elseif($Param['type']=='float' || $Param['type']=='floatdiapazon'){
                $Param['multiple'] = true;
                $Param['type'] = 'floatdiapazon';
                $Param['val'] = $p->getFloatnumber();
            }
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