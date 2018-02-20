<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="company")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompanyRepository")
 */
class Company
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
    private $logo;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $preview;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $info;
    
    /**
     * @ORM\Column(type="json_array")
     */
    private $phones = [];
    
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;
            
    /**
     * @ORM\OneToMany(targetEntity="Object", mappedBy="company")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $objects;
    
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getLogo() {
        return $this->logo;
    }

    function getPreview() {
        return $this->preview;
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

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setLogo($logo) {
        $this->logo = $logo;
    }

    function setPreview($preview) {
        $this->preview = $preview;
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
    /**
     * 
     * @return Object
     */
    function getObjects() {
        return $this->objects;
    }

    function setObjects($objects) {
        $this->objects = $objects;
    }
    
    public function __toString()
    {
        return $this->getName();
    }

}