<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $dateNaiss;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $adresseCab;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $dateDip;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $maladie;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $pseudo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="personne", orphanRemoval=true)
     */
    private $articles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="personnes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consultation", mappedBy="medecin")
     */
    private $consultations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Consultation", mappedBy="user",orphanRemoval=true)
     */
    private $consultationsOfUser;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="abonnants")
     */
    // mes abonnes
    private $abonnes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="abonnes")
     */
    // les personnes eli 3meltlhom abonner
    private $abonnants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reaction", mappedBy="user", orphanRemoval=true)
     */
    private $reacts;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->consultationsOfUser = new ArrayCollection();
        $this->abonnes = new ArrayCollection();
        $this->abonnants = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->reacts = new ArrayCollection();
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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = $this->getRole()->getNomRole();

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
        return (string) $this->password;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaiss(): ?string
    {
        return $this->dateNaiss;
    }

    public function setDateNaiss(?string $dateNaiss): self
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getAdresseCab(): ?string
    {
        return $this->adresseCab;
    }

    public function setAdresseCab(?string $adresseCab): self
    {
        $this->adresseCab = $adresseCab;

        return $this;
    }

    public function getDateDip(): ?string
    {
        return $this->dateDip;
    }

    public function setDateDip(?string $dateDip): self
    {
        $this->dateDip = $dateDip;

        return $this;
    }

    public function getMaladie(): ?string
    {
        return $this->maladie;
    }

    public function setMaladie(?string $maladie): self
    {
        $this->maladie = $maladie;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setPersonne($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getPersonne() === $this) {
                $article->setPersonne(null);
            }
        }

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection|Consultation[]
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations[] = $consultation;
            $consultation->setMedecin($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->contains($consultation)) {
            $this->consultations->removeElement($consultation);
            // set the owning side to null (unless already changed)
            if ($consultation->getMedecin() === $this) {
                $consultation->setMedecin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Consultation[]
     */
    public function getConsultationsOfUser(): Collection
    {
        return $this->consultationsOfUser;
    }

    public function addConsultationsOfUser(Consultation $consultationsOfUser): self
    {
        if (!$this->consultationsOfUser->contains($consultationsOfUser)) {
            $this->consultationsOfUser[] = $consultationsOfUser;
            $consultationsOfUser->setUser($this);
        }

        return $this;
    }

    public function removeConsultationsOfUser(Consultation $consultationsOfUser): self
    {
        if ($this->consultationsOfUser->contains($consultationsOfUser)) {
            $this->consultationsOfUser->removeElement($consultationsOfUser);
            // set the owning side to null (unless already changed)
            if ($consultationsOfUser->getUser() === $this) {
                $consultationsOfUser->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAbonnes(): Collection
    {
        return $this->abonnes;
    }

    public function addAbonne(self $abonne): self
    {
        if (!$this->abonnes->contains($abonne)) {
            $this->abonnes[] = $abonne;
        }

        return $this;
    }

    public function removeAbonne(self $abonne): self
    {
        if ($this->abonnes->contains($abonne)) {
            $this->abonnes->removeElement($abonne);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getAbonnants(): Collection
    {
        return $this->abonnants;
    }

    public function addAbonnant(self $abonnant): self
    {
        if (!$this->abonnants->contains($abonnant)) {
            $this->abonnants[] = $abonnant;
            $abonnant->addAbonne($this);
        }

        return $this;
    }

    public function removeAbonnant(self $abonnant): self
    {
        if ($this->abonnants->contains($abonnant)) {
            $this->abonnants->removeElement($abonnant);
            $abonnant->removeAbonne($this);
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reaction[]
     */
    public function getReacts(): Collection
    {
        return $this->reacts;
    }

    public function addReact(Reaction $react): self
    {
        if (!$this->reacts->contains($react)) {
            $this->reacts[] = $react;
            $react->setUser($this);
        }

        return $this;
    }

    public function removeReact(Reaction $react): self
    {
        if ($this->reacts->contains($react)) {
            $this->reacts->removeElement($react);
            // set the owning side to null (unless already changed)
            if ($react->getUser() === $this) {
                $react->setUser(null);
            }
        }

        return $this;
    }
}
