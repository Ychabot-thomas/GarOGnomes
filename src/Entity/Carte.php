<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarteRepository")
 */
class Carte
{
    public const COULEUR_BLEU = 1;
    public const COULEUR_ROUGE = 2;
    public const COULEUR_VERT = 3;

    public const ARME_PIERRE = 1;
    public const ARME_FEUILLE = 2;
    public const ARME_CISEAUX = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $couleur;

    /**
     * @ORM\Column(type="integer")
     */
    private $poid;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $camps;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCouleur(): ?int
    {
        return $this->couleur;
    }

    public function setCouleur(int $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getPoid(): ?int
    {
        return $this->poid;
    }

    public function setPoid(int $poid): self
    {
        $this->poid = $poid;

        return $this;
    }

    public function getCamps(): ?string
    {
        return $this->camps;
    }

    public function setCamps(string $camps): self
    {
        $this->camps = $camps;

        return $this;
    }

    public function isShogun()
    {
        return $this->couleur === 4;
    }

    public function couleurTexte()
    {
        switch ($this->getCouleur()) {
            case 1:
                return 'blue';
            case 2:
                return 'red';
            case 3:
                return 'green';
            default:
                return 'shogun';
        }
    }

    public function type()
    {
        switch ($this->getCouleur()) {
            case 1:
                return 'P';
            case 2:
                return 'C';
            case 3:
                return 'F';
            default:
                return 'Sh';
        }
    }

    public function isPierre()
    {
        return $this->getCouleur() === 1;
    }

    public function isPapier()
    {
        return $this->getCouleur() === 3;
    }

    public function isCiseaux()
    {
        return $this->getCouleur() === 2;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
