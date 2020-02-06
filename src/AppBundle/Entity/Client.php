<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 */
class Client
{    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $info;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $owner;
    
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $phones = [];
    
    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="clients")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastUpdate;
    
    /**
     * @ORM\OneToMany(targetEntity="Object", mappedBy="client", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $objects;
    
    /**
     * @ORM\OneToMany(targetEntity="Bid", mappedBy="client", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $bids;
    
    /**
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="client", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $tickets;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $created;
            
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getPhoto() {
        return $this->photo;
    }

    function getInfo() {
        return $this->info;
    }

    function getPhones() {
        return $this->phones;
    }

    function getEmail() {
        return $this->email;
    }

    function getUser() {
        return $this->user;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setPhoto($photo) {
        $this->photo = $photo;
    }

    function setInfo($info) {
        $this->info = $info;
    }

    function setPhones($phones) {
        $this->phones = $phones;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setUser(User $user) {
        $this->user= $user;
    }
    
    function getLastUpdate() {
        return $this->lastUpdate;
    }

    function setLastUpdate($lastUpdate) {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return ArrayCollection|Object[]
     */
    function getObjects() {
        return $this->objects;
    }

    /**
     * @return ArrayCollection|Ticket[]
     */
    function getTickets() {
        return $this->tickets;
    }

    function setObjects($objects) {
        $this->objects = $objects;
    }
 
    function getBids() {
        return $this->bids;
    }

    function setBids($bids) {
        $this->bids = $bids;
    }
    
    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    
    function getOwner() {
        return $this->owner;
    }

    function setOwner($owner) {
        $this->owner = $owner;
    }

    function getType() {
        return $this->type;
    }

    function setType($type) {
        $this->type = $type;
    }

    public function __toString()
    {
        $name = $this->getName().' (№'.$this->getId();
        if($this->getUser()){
            $name .= ' від '.$this->getUser()->getName();
        }
        $name .= ') ';
        if($this->getPhones()){
            $name .= implode(", ",$this->getPhones());
        }
        return $name;
    }
    
    public function canEdit(User $user){
        if($this->getUser() && $this->getUser()->getId() == $user->getId())
            return true;
        if(in_array('ROLE_SUPERADMIN', $user->getRoles()))
            return true;
        return false;
    }
    
    function getCreated() {
        return $this->created;
    }

    function setCreated($created) {
        $this->created = $created;
    }

}