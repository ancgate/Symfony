<?php

// src/OC/PlatformBundle/Entity/Image


namespace MerQury\PlateformBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Entity\ImageRepository")
 */

class Image

{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */

  private $id;
  /**
   * @ORM\Column(name="url", type="string", length=255)
   */

  private $url;
  /**
   * @ORM\Column(name="alt", type="string", length=255)
   */

  private $alt;
  function getId() {
      return $this->id;
  }

  function getUrl() {
      return $this->url;
  }

  function getAlt() {
      return $this->alt;
  }

  function setId($id) {
      $this->id = $id;
  }

  function setUrl($url) {
      $this->url = $url;
  }

  function setAlt($alt) {
      $this->alt = $alt;
  }



}