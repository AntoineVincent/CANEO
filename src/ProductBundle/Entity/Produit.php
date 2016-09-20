<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="ProductBundle\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var float
     *
     * @ORM\Column(name="prixminimal", type="float", nullable=true)
     */
    private $prixminimal;

    /**
     * @var int
     *
     * @ORM\Column(name="commandemaximal", type="integer")
     */
    private $commandemaximal;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255)
     */
    private $photo;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Produit
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
     * Set prixminimal
     *
     * @param float $prixminimal
     * @return Produit
     */
    public function setPrixminimal($prixminimal)
    {
        $this->prixminimal = $prixminimal;

        return $this;
    }

    /**
     * Get prixminimal
     *
     * @return float 
     */
    public function getPrixminimal()
    {
        return $this->prixminimal;
    }

    /**
     * Set commandemaximal
     *
     * @param integer $commandemaximal
     * @return Produit
     */
    public function setCommandemaximal($commandemaximal)
    {
        $this->commandemaximal = $commandemaximal;

        return $this;
    }

    /**
     * Get commandemaximal
     *
     * @return integer 
     */
    public function getCommandemaximal()
    {
        return $this->commandemaximal;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Produit
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }
}
