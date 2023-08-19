<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsultationRepository")
 */
class Consultation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=11)
     */
    private $DateConsul;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="consultations")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $medecin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="consultationsOfUser")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateConsul(): ?string
    {
        return $this->DateConsul;
    }

    public function setDateConsul(string $DateConsul): self
    {
        $this->DateConsul = $DateConsul;

        return $this;
    }

    public function getMedecin(): ?User
    {
        return $this->medecin;
    }

    public function setMedecin(?User $medecin): self
    {
        $this->medecin = $medecin;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
