<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    public $type;
    /**
     * @var string
     *
     * @ORM\Column(name="adresselivraison", type="string", length=255, nullable=true)
     */
    public $adresselivraison;
    /**
     * @var string
     *
     * @ORM\Column(name="adressefactu", type="string", length=255, nullable=true)
     */
    public $adressefactu;
    /**
     * @var string
     *
     * @ORM\Column(name="mailbis", type="string", length=255, nullable=true)
     */
    public $mailbis;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * Set type
     *
     * @param string $type
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set adresselivraison
     *
     * @param string $adresselivraison
     * @return User
     */
    public function setAdresselivraison($adresselivraison)
    {
        $this->adresselivraison = $adresselivraison;

        return $this;
    }

    /**
     * Get adresselivraison
     *
     * @return string 
     */
    public function getAdresselivraison()
    {
        return $this->adresselivraison;
    }

    /**
     * Set adressefactu
     *
     * @param string $adressefactu
     * @return User
     */
    public function setAdressefactu($adressefactu)
    {
        $this->adressefactu = $adressefactu;

        return $this;
    }

    /**
     * Get adressefactu
     *
     * @return string 
     */
    public function getAdressefactu()
    {
        return $this->adressefactu;
    }

    /**
     * Set mailbis
     *
     * @param string $mailbis
     * @return User
     */
    public function setMailbis($mailbis)
    {
        $this->mailbis = $mailbis;

        return $this;
    }

    /**
     * Get mailbis
     *
     * @return string 
     */
    public function getMailbis()
    {
        return $this->mailbis;
    }
}
