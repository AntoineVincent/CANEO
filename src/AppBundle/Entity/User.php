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
    private $type;
    /**
     * @var string
     *
     * @ORM\Column(name="adresselivraison", type="string", length=255, nullable=true)
     */
    private $adresselivraison;
    /**
     * @var string
     *
     * @ORM\Column(name="adressefactu", type="string", length=255, nullable=true)
     */
    private $adressefactu;
    /**
     * @var string
     *
     * @ORM\Column(name="mailbis", type="string", length=255, nullable=true)
     */
    private $mailbis;
    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;
    /**
     * @var string
     *
     * @ORM\Column(name="infos", type="string", length=255, nullable=true)
     */
    private $infos;
    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;
    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;
    /**
     * @var int
     *
     * @ORM\Column(name="cpfactu", type="integer", nullable=true)
     */
    private $cpfactu;
    /**
     * @var string
     *
     * @ORM\Column(name="villefactu", type="string", length=255, nullable=true)
     */
    private $villefactu;
    /**
     * @var int
     *
     * @ORM\Column(name="cplivraison", type="integer", nullable=true)
     */
    private $cplivraison;
    /**
     * @var string
     *
     * @ORM\Column(name="villelivraison", type="string", length=255, nullable=true)
     */
    private $villelivraison;
    /**
     * @var integer
     *
     * @ORM\Column(name="notifs", type="integer", nullable=true)
     */
    private $notifs;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public static function getAllRoles()
    {
        return array('ROLE_ADMIN', 'ROLE_USER');
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

    /**
     * Set logo
     *
     * @param string $logo
     * @return User
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set infos
     *
     * @param string $infos
     * @return User
     */
    public function setInfos($infos)
    {
        $this->infos = $infos;

        return $this;
    }

    /**
     * Get infos
     *
     * @return string 
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set cpfactu
     *
     * @param integer $cpfactu
     * @return User
     */
    public function setCpfactu($cpfactu)
    {
        $this->cpfactu = $cpfactu;

        return $this;
    }

    /**
     * Get cpfactu
     *
     * @return integer 
     */
    public function getCpfactu()
    {
        return $this->cpfactu;
    }

    /**
     * Set villefactu
     *
     * @param string $villefactu
     * @return User
     */
    public function setVillefactu($villefactu)
    {
        $this->villefactu = $villefactu;

        return $this;
    }

    /**
     * Get villefactu
     *
     * @return string 
     */
    public function getVillefactu()
    {
        return $this->villefactu;
    }

    /**
     * Set cplivraison
     *
     * @param integer $cplivraison
     * @return User
     */
    public function setCplivraison($cplivraison)
    {
        $this->cplivraison = $cplivraison;

        return $this;
    }

    /**
     * Get cplivraison
     *
     * @return integer 
     */
    public function getCplivraison()
    {
        return $this->cplivraison;
    }

    /**
     * Set villelivraison
     *
     * @param string $villelivraison
     * @return User
     */
    public function setVillelivraison($villelivraison)
    {
        $this->villelivraison = $villelivraison;

        return $this;
    }

    /**
     * Get villelivraison
     *
     * @return string 
     */
    public function getVillelivraison()
    {
        return $this->villelivraison;
    }

    /**
     * Set notifs
     *
     * @param integer $notifs
     * @return User
     */
    public function setNotifs($notifs)
    {
        $this->notifs = $notifs;

        return $this;
    }

    /**
     * Get notifs
     *
     * @return integer 
     */
    public function getNotifs()
    {
        return $this->notifs;
    }
}
