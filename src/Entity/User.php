<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

/**
 * @property plainPassword
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo a déjà été pris")
 * @UniqueEntity(fields={"email"}, message="Cette email correspond déjà à un compte")
 * @method getDoctrine()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $pseudo;
    /**
     * @ORM\Column(type="datetime")
     */
    private $dateinscription;
    /**
     * @ORM\Column(type="datetime")
     */
    private $derniereco;
    /**
     * @ORM\Column(type="boolean")
     */
    private $bloquer;
    /**
     * @ORM\Column(type="string")
     * @CaptchaAssert\ValidCaptcha(message = "CAPTCHA invalide, réessayer.")
     */
    protected $captchaCode;
    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $resetToken;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partie", mappedBy="j1_partie")
     */
    private $partiej1;
    /**
     * @return mixed
     */
    public function getPartiej1()
    {
        return $this->partiej1;
    }
    /**
     * @param mixed $partiej1
     */
    public function setPartiej1($partiej1): void
    {
        $this->partiej1 = $partiej1;
    }
    /**
     * @return mixed
     */
    public function getPartiej2()
    {
        return $this->partiej2;
    }
    /**
     * @param mixed $partiej2
     */
    public function setPartiej2($partiej2): void
    {
        $this->partiej2 = $partiej2;
    }
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partie", mappedBy="j2_partie")
     */
    private $partiej2;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partie", mappedBy="tour")
     */
    private $tour;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partie", mappedBy="gagnant_partie")
     */
    private $gagnantpartie;

    /**
     * @return mixed
     */
    public function getGagnantpartie()
    {
        return $this->gagnantpartie;
    }
    /**
     * @param mixed $gagnantpartie
     */
    public function setGagnantpartie($gagnantpartie): void
    {
        $this->gagnantpartie = $gagnantpartie;
    }
    public function __construct()
    {
        $this->tour = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        if ($this->bloquer === false) {
            if ($this->pseudo === "admin") {
                $roles[] = 'ROLE_ADMIN';
            } else {
                // guarantee every user at least has ROLE_USER
                $roles[] = 'ROLE_USER';
            }
        } elseif ($this->bloquer === true) {
            throw new CustomUserMessageAuthenticationException('Votre compte a été bloqué par l\'administrateur !');
        }
        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;
        return $this;
    }
    public function getDateinscription(): ?\DateTimeInterface
    {
        return $this->dateinscription;
    }
    public function setDateinscription(\DateTimeInterface $dateinscription): self
    {
        $this->dateinscription = $dateinscription;
        return $this;
    }
    public function getDerniereco(): ?\DateTimeInterface
    {
        return $this->derniereco;
    }
    public function setDerniereco(\DateTimeInterface $derniereco): self
    {
        $this->derniereco = $derniereco;
        return $this;
    }
    public function getBloquer(): ?bool
    {
        return $this->bloquer;
    }
    public function setBloquer(bool $bloquer): self
    {
        $this->bloquer = $bloquer;
        return $this;
    }
    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }
    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }
    /**
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }
    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }
    /**
     * @return Collection|Partie[]
     */
    public function getTour(): Collection
    {
        return $this->tour;
    }
    public function addTour(Partie $tour): self
    {
        if (!$this->tour->contains($tour)) {
            $this->tour[] = $tour;
            $tour->setTour($this);
        }
        return $this;
    }
    public function removeTour(Partie $tour): self
    {
        if ($this->tour->contains($tour)) {
            $this->tour->removeElement($tour);
            // set the owning side to null (unless already changed)
            if ($tour->getTour() === $this) {
                $tour->setTour(null);
            }
        }
        return $this;
    }
}
