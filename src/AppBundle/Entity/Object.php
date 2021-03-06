<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="object")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObjectRepository")
 */
class Object
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
    private $code;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rooms;
    
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $area;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $important = false;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $saletype = false;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $exclusive = false;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $advertising = false;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $domria = false;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $domria_id = false;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $comission = false;
    
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $photos;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $info;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $officialinfo;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $address;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="objects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="objects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="objects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $company;
    
    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="objects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $location;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastUpdate;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $created;
        
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="objects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $created_by;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $baseprice;
    
    /**
     * @Assert\Range(min=0, minMessage="Вставновіть мінімальну ціну")
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;
    
    /**
     * @Assert\Range(min=0, minMessage="Вставновіть мінімальну ціну")
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_uah;
    
    /**
     * @Assert\Range(min=0, minMessage="Вставновіть мінімальну ціну за м2")
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_m2;
    
    /**
     * @Assert\Range(min=0, minMessage="Вставновіть мінімальну ціну за м2")
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_m2_uah;
    
    /**
     * @ORM\OneToMany(targetEntity="ObjectParam", mappedBy="object", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $params;
    
    /**
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="object", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $tickets;
    
    /**
     * @ORM\OneToMany(targetEntity="Favourite", mappedBy="object", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $favourites;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getPhotos() {
        return array_values($this->photos);
    }

    function getInfo() {
        return $this->info;
    }

    function getAddress() {
        return $this->address;
    }

    /**
     * @return ArrayCollection|User[]
     */
    function getUser() {
        return $this->user;
    }

    /**
     * @return ArrayCollection|Client[]
     */
    function getClient() {
        return $this->client;
    }
    /**
     * @return ArrayCollection|Ticket[]
     */
    function getTickets() {
        return $this->tickets;
    }

    function getLastUpdate() {
        return $this->lastUpdate;
    }

    function getPrice() {
        return $this->price;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPhotos($photos) {
        $this->photos = $photos;
    }

    function setInfo($info) {
        $this->info = $info;
    }

    function setAddress($address) {
        $this->address = $address;
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

    function setPrice($price) {
        $this->price = $price;
    }

    function getImportant() {
        return $this->important;
    }

    function setImportant($important) {
        $this->important = $important;
    }

    /**
     * @return ArrayCollection|ObjectParam[]
     */
    function getParams() {
        return $this->params;
    }
    
    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    
    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
    }
    
    function getLocation() {
        return $this->location;
    }

    function setLocation($location) {
        $this->location = $location;
    }
    
    function getRooms() {
        return $this->rooms;
    }

    function getArea() {
        return $this->area;
    }

    function setRooms($rooms) {
        $this->rooms = $rooms;
    }

    function setArea($area) {
        $this->area = trim(str_replace(",", ".", $area));
    }
    
    function getCode() {
        return $this->code;
    }

    function setCode($code) {
        $this->code = $code;
    }
    
    function getExclusive() {
        return $this->exclusive;
    }

    function setExclusive($exclusive) {
        $this->exclusive = $exclusive;
    }
    
    function getAdvertising() {
        return $this->advertising;
    }

    function setAdvertising($advertising) {
        $this->advertising = $advertising;
    }
    /**
     * 
     * @return Company
     */
    function getCompany() {
        return $this->company;
    }

    function setCompany($company) {
        $this->company = $company;
    }
    
    function getOfficialinfo() {
        return $this->officialinfo;
    }

    function setOfficialinfo($officialinfo) {
        $this->officialinfo = $officialinfo;
    }
    
    function getDomria() {
        return $this->domria;
    }

    function setDomria($domria) {
        $this->domria = $domria;
    }
    
    function getPriceUah() {
        return $this->price_uah;
    }

    function getPriceM2() {
        return $this->price_m2;
    }

    function getPriceM2Uah() {
        return $this->price_m2_uah;
    }

    function setPriceUah($price_uah) {
        $this->price_uah = $price_uah;
    }

    function setPriceM2($price_m2) {
        $this->price_m2 = $price_m2;
    }

    function setPriceM2Uah($price_m2_uah) {
        $this->price_m2_uah = $price_m2_uah;
    }
    function getComission() {
        return $this->comission;
    }

    function setComission($comission) {
        $this->comission = $comission;
    }
    
    function getSaletype() {
        return $this->saletype;
    }

    function setSaletype($saletype) {
        $this->saletype = $saletype;
    }
    
    function getBaseprice() {
        return $this->baseprice;
    }

    function setBaseprice($baseprice) {
        $this->baseprice = $baseprice;
    }
    
    function getCreated() {
        return $this->created;
    }

    function setCreated($created) {
        $this->created = $created;
    }
    
    function getFavourites() {
        return $this->favourites;
    }

    function setFavourites($favourites) {
        $this->favourites = $favourites;
    }

    function getCreatedBy() {
        return $this->created_by;
    }

    function setCreatedBy($created_by) {
        $this->created_by = $created_by;
    }
    
    function getDomriaId() {
        return $this->domria_id;
    }

    function setDomriaId($domria_id) {
        $this->domria_id = $domria_id;
    }
    
    function isFavouriteInUser(User $user){
        $favs = $this->getFavourites();
        foreach ($favs as $fav){
            if($fav->getUser() == $user)
                return true;
        }
        return false;
    }
    
    function canEdit(User $user){
        if($this->getUser() && $this->getUser()->getId() == $user->getId())
            return true;
        if(in_array('ROLE_SUPERADMIN', $user->getRoles()))
            return true;
        return false;
    }
    
    function getParamVal($id){
        $params = $this->getParams();
        $res = [];
        foreach($params as $param){
            if($param->getParam()->getId() == $id){
                switch ($param->getParam()->getType()){
                    case 'select': if($param->getProperty()) $res[] = $param->getProperty()->getName();break;
                    case 'text': $res[] = $param->getString(); break;
                    case 'integer': $res[] = $param->getNumber(); break;
                    case 'float': $res[] = $param->getFloatnumber(); break;
                }
            }
        }
        return implode(', ',$res);
    }
    
    public function __toString() {
        $name = $this->getName();
        if($this->getCode()!="")
            $name .= ' ('.$this->getCode().')';
        return $name;
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
            elseif($Param['type']=='float' || $Param['type']=='floatdiapazon')
                $Param['val'] = $p->getFloatnumber();
            elseif($Param['type']=='text')
                $Param['val'] = $p->getString();
            else{
                if($p->getProperty())
                    $Param['val'] = $p->getProperty()->getId();
                else
                    $Param = false;
            }
            
            if($Param)
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