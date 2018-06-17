<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="favourite")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FavouriteRepository")
 */
class Favourite {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Object", inversedBy="favourites")
     * @ORM\JoinColumn(nullable=true)
     */
    private $object;

    /**
     * @ORM\ManyToOne(targetEntity="Bid", inversedBy="favourites")
     * @ORM\JoinColumn(nullable=true)
     */
    private $bid;

    function getId() {
        return $this->id;
    }

    function getUser() {
        return $this->user;
    }

    function getObject() {
        return $this->object;
    }

    function getBid() {
        return $this->bid;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setObject($object) {
        $this->object = $object;
    }

    function setBid($bid) {
        $this->bid = $bid;
    }
}
