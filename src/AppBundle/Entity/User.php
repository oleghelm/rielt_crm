<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="It looks like your already have an account!")
 */
class User implements UserInterface
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
     * The encoded password
     * 
     * @ORM\Column(type="string")
     */
    private $password;
    
    /**
     * A non-persisted field that's used to create the encoded password.
     * 
     * @var string
     * @Assert\NotBlank(groups={"Registration"})
     */
    private $plainPassword;
    
    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];
    
    /**
     * @ORM\OneToMany(targetEntity="Client", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $clients;

    private $username;
        
    /**
     * @ORM\OneToMany(targetEntity="Object", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $objects;
    
    /**
     * @ORM\OneToMany(targetEntity="Bid", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $bids;
    
    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
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

    function setName($name) {
        $this->name = $name;
    }

    function setPhoto($photo) {
        $this->photo = $photo;
//        return $this;
    }

    function setInfo($info) {
        $this->info = $info;
    }

    function setPhones($phones) {
        $this->phones = $phones;
    }

    function getEmail() {
        return $this->email;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword(){
        return $this->password;
    }
    
    function setPassword($password) {
        $this->password = $password;
    }
    
    public function getUsername(){
        return $this->username;
    }
    
    public function setUsername($username){
        $this->username = $username;
    }

    function getPlainPassword(){
        return $this->plainPassword;
    }

    function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function getRoles() {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }
    
    function setRoles($roles) {
        $this->roles = $roles;
    }
    
    /**
     * @return ArrayCollection|Client[]
     */
    function getClients() {
        return $this->clients;
    }
    
    public function eraseCredentials() {
        $this->plainPassword = null;
    }

    function getObjects() {
        return $this->objects;
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

    public function getSalt() {
        
    }
    public function __toString()
    {
        return $this->getName();
    }

}