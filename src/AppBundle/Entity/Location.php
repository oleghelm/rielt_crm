<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LocationRepository")
 */
class Location
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
    private $nameru;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;
    
    /**
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="locations")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $location;
    
    /**
     * @ORM\OneToMany(targetEntity="Location", mappedBy="location")
     * @ORM\JoinColumn(nullable=true)
     */
    private $locations;

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getLevel() {
        return $this->level;
    }

    function getNote() {
        return $this->note;
    }

    function getLocation() {
        return $this->location;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setLevel($level) {
        $this->level = $level;
    }

    function setNote($note) {
        $this->note = $note;
    }

    function setLocation($location) {
        $this->location = $location;
    }
    /**
     * @return ArrayCollection|Location[]
     */
    function getLocations() {
        return $this->locations;
    }

    function setLocations($locations) {
        $this->locations = $locations;
    }
    
    function getNameru() {
        return $this->nameru;
    }

    function setNameru($nameru) {
        $this->nameru = $nameru;
    }
    
    function getLevelName(){
        $names = [
            'Країна' => 0,
            'Область' => 1,
            'Район області' => 2,
            'Місто' => 3,
            'Район міста' => 4,
            'Вулиця' => 5,
            'Село' => 6,
            'Вулиця села' => 7,
        ];
        return array_search($this->getLevel(), $names);
    }
    
    public function __toString()
    {
        return $this->getName();
    }

}