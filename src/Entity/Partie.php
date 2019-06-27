<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 */
class Partie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="partiej1")
     */
    private $j1_partie;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="partiej2")
     */
    private $j2_partie;
    /**
     * @ORM\Column(type="datetime")
     */
    private $debut;
    /**
     * @ORM\Column(type="json_array")
     */
    private $terrainJ1;
    /**
     * @ORM\Column(type="json_array")
     */
    private $terrainJ2;
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $des;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="gagnantpartie")
     */
    private $gagnant_partie;
    /**
     * @ORM\Column(type="time")
     */
    private $timePartie;
    /**
     * @ORM\Column(type="text")
     */
    private $move;
    /**
     * @ORM\Column(type="boolean")
     */
    private $etatpartie;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tour")
     */
    private $tour;
    public function __construct()
    {
        $this->debut = new \DateTime('now');
        $this->users = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getj1_partie(): ?User
    {
        return $this->j1_partie;
    }
    public function getJ1Partie(): ?User
    {
        return $this->getj1_partie();
    }
    public function setj1_partie(?User $j1_partie): self
    {
        $this->j1_partie = $j1_partie;
        return $this;
    }
    public function getj2_partie(): ?User
    {
        return $this->j2_partie;
    }
    public function getJ2Partie(): ?User
    {
        return $this->j2_partie;
    }
    public function setj2_partie(?User $j2_partie): self
    {
        $this->j2_partie = $j2_partie;
        return $this;
    }
    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }
    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;
        return $this;
    }
    public function getTerrainJ1()
    {
        return $this->terrainJ1;
    }
    public function setTerrainJ1($terrainJ1): self
    {
        $this->terrainJ1 = $terrainJ1;
        return $this;
    }
    public function getTerrainJ2()
    {
        return $this->terrainJ2;
    }
    public function setTerrainJ2($terrainJ2): self
    {
        $this->terrainJ2 = $terrainJ2;
        return $this;
    }
    public function getDes()
    {
        return $this->des;
    }
    public function setDes($des): self
    {
        $this->des = $des;
        return $this;
    }
    /**
     * @return mixed
     */
    public function getGagnantPartie(): ?user
    {
        return $this->gagnant_partie;
    }
    /**
     * @param mixed $gagnant_partie
     * @return Partie
     */
    public function setGagnantPartie(?User $gagnant_partie): self
    {
        $this->gagnant_partie = $gagnant_partie;
        return $this;
    }
    public function getTimePartie(): ?\DateTimeInterface
    {
        return $this->timePartie;
    }
    public function setTimePartie(\DateTimeInterface $timePartie): self
    {
        $this->timePartie = $timePartie;
        return $this;
    }
    public function getMove(): ?string
    {
        return $this->move;
    }
    public function setMove(string $move): self
    {
        $this->move = $move;
        return $this;
    }
    public function getEtatpartie(): ?bool
    {
        return $this->etatpartie;
    }
    public function setEtatpartie(bool $etatpartie): self
    {
        $this->etatpartie = $etatpartie;
        return $this;
    }
    public function getTour(): ?User
    {
        return $this->tour;
    }
    public function setTour(?User $tour): self
    {
        $this->tour = $tour;
        return $this;
    }
}
