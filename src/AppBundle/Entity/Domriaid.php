<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Location;
use AppBundle\Repository\LocationRepository;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="domriaid")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DomriaidRepository")
 */
class Domriaid
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
    private $did;
    
    function getId() {
        return $this->id;
    }

    function getDid() {
        return $this->did;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDid($did) {
        $this->did = $did;
    }

}