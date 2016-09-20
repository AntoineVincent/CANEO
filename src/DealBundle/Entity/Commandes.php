<?php

namespace DealBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commandes
 *
 * @ORM\Table(name="commandes")
 * @ORM\Entity(repositoryClass="DealBundle\Repository\CommandesRepository")
 */
class Commandes
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
     * @var int
     *
     * @ORM\Column(name="idenchere", type="integer", nullable=true)
     */
    private $idenchere;

    /**
     * @var int
     *
     * @ORM\Column(name="idacheteur", type="integer", nullable=true)
     */
    private $idacheteur;

    /**
     * @var int
     *
     * @ORM\Column(name="nbredecommande", type="integer", nullable=true)
     */
    private $nbredecommande;


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
     * Set idenchere
     *
     * @param integer $idenchere
     * @return Commandes
     */
    public function setIdenchere($idenchere)
    {
        $this->idenchere = $idenchere;

        return $this;
    }

    /**
     * Get idenchere
     *
     * @return integer 
     */
    public function getIdenchere()
    {
        return $this->idenchere;
    }

    /**
     * Set idacheteur
     *
     * @param integer $idacheteur
     * @return Commandes
     */
    public function setIdacheteur($idacheteur)
    {
        $this->idacheteur = $idacheteur;

        return $this;
    }

    /**
     * Get idacheteur
     *
     * @return integer 
     */
    public function getIdacheteur()
    {
        return $this->idacheteur;
    }

    /**
     * Set nbredecommande
     *
     * @param integer $nbredecommande
     * @return Commandes
     */
    public function setNbredecommande($nbredecommande)
    {
        $this->nbredecommande = $nbredecommande;

        return $this;
    }

    /**
     * Get nbredecommande
     *
     * @return integer 
     */
    public function getNbredecommande()
    {
        return $this->nbredecommande;
    }
}
