<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="agreement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgreementRepository")
 */
class Agreement {

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
    private $info;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Object")
     * @ORM\JoinColumn(nullable=true)
     */
    private $object;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comision;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(type="json_array")
     */
    private $flags = [];

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getInfo() {
        return $this->info;
    }

    function getUser() {
        return $this->user;
    }

    function getClient() {
        return $this->client;
    }

    function getObject() {
        return $this->object;
    }

    function getDate() {
        return $this->date;
    }

    function getPrice() {
        return $this->price;
    }

    function getComision() {
        return $this->comision;
    }

    function getTotal() {
        return $this->total;
    }
    /**
     * @return ArrayCollection
     */
    function getFlags() {
        return $this->flags;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
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

    function setObject($object) {
        $this->object = $object;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setPrice($price) {
        $this->price = $price;
    }

    function setComision($comision) {
        $this->comision = $comision;
    }

    function setTotal($total) {
        $this->total = $total;
    }

    function setFlags($flags) {
        $this->flags = $flags;
    }
    
    function getStatus() {
        return $this->status;
    }

    function setStatus($status) {
        $this->status = $status;
    }

}
