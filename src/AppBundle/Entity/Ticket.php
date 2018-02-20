<?php

namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TicketRepository")
 */
class Ticket{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
        
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $time;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", nullable=true)
     */
    private $place;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $clientName;
    
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", nullable=true)
     */
    private $task;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $result;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $info;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tickets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Bid")
     * @ORM\JoinColumn(nullable=true)
     */
    private $bid;
    
    /**
     * @ORM\ManyToOne(targetEntity="Object")
     * @ORM\JoinColumn(nullable=true)
     */
    private $object;
    
    function getId() {
        return $this->id;
    }

    function getDate() {
        return $this->date;
    }

    function getCreated() {
        return $this->created;
    }

    function getPlace() {
        return $this->place;
    }

    function getClientName() {
        return $this->clientName;
    }

    function getTask() {
        return $this->task;
    }
    function getTaskTranslate() {
        $names = [
            'meet' => 'Зустріч',
            'show' => "Показ об'єкту",
            'creating' => "Оформлення нового об'єкту",
            'docs' => "Оформлення документів",
            'standup' => "Збори в офісі",
        ];
        return $names[$this->getTask()];
    }

    function getResult() {
        return $this->result;
    }

    function getStatus() {
        return $this->status;
    }
    
    function getStatusTranslate() {
        $names = [
            'new' => 'Новий',
            'inwork' => "В роботі",
            'replace' => "Перенесено",
            'done' => "Виконано",
            'cancel' => "Відмінено",
        ];
        return $names[$this->getStatus()];
    }

    function getInfo() {
        return $this->info;
    }

    function getClient() {
        return $this->client;
    }

    function getUser() {
        return $this->user;
    }

    function getBid() {
        return $this->bid;
    }

    function getObject() {
        return $this->object;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDate($date) {
        $this->date = $date;
    }

    function setCreated($created) {
        $this->created = $created;
    }

    function setPlace($place) {
        $this->place = $place;
    }

    function setClientName($clientName) {
        $this->clientName = $clientName;
    }

    function setTask($task) {
        $this->task = $task;
    }

    function setResult($result) {
        $this->result = $result;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setInfo($info) {
        $this->info = $info;
    }

    function setClient($client) {
        $this->client = $client;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setBid($bid) {
        $this->bid = $bid;
    }

    function setObject($object) {
        $this->object = $object;
    }

    function getTime() {
        return $this->time;
    }

    function setTime($time) {
        $this->time = $time;
    }
    
    public function __toString()
    {
        return $this->getId();
    }
}