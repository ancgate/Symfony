<?php

// src/OC/PlatformBundle/Entity/Application.php


namespace MerQury\PlateformBundle\Entity;
use MerQury\PlateformBundle\Entity\Advert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="MerQury\PlateformBundle\Entity\ApplicationRepository")
 */
class Application
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;
  
  /**
   * @ORM\Column(name="author", type="string", length=255)
   */
  private $author;
  
  /**
   * @ORM\Column(name="content", type="text")
   */
  private $content;
  
  
  /**
   * @ORM\Column(name="date", type="datetime")
   */
  private $date;
  
  
    /**
   * @ORM\ManyToOne(targetEntity="MerQury\PlateformBundle\Entity\Advert", inversedBy="applications")
   * @ORM\JoinColumn(nullable=false)
   * @ORM\HasLifecycleCallbacks()
   */
  private $advert;
  
  
    /**
   * @ORM\PrePersist
   */
  public function increase()
  {
    $this->getAdvert()->increaseApplication();
  }

  /**
   * @ORM\PreRemove
   */
  public function decrease()
  {
    $this->getAdvert()->decreaseApplication();
  }

  
  
  
  function getAdvert() {
      return $this->advert;
  }

  function setAdvert($advert) {
      $this->advert = $advert;
  }

    
  function getId() {
      return $this->id;
  }

  function getAuthor() {
      return $this->author;
  }

  function getContent() {
      return $this->content;
  }

  function getDate() {
      return $this->date;
  }

  function setId($id) {
      $this->id = $id;
  }

  function setAuthor($author) {
      $this->author = $author;
  }

  function setContent($content) {
      $this->content = $content;
  }

  function setDate($date) {
      $this->date = $date;
  }

    

  public function __construct()

  {
    $this->date = new \Datetime();
  }
  
}
