<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReactionRepository")
 */
class Reaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_like;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Article", inversedBy="reactions")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reacts")
     * @ORM\JoinColumn(nullable=false,onDelete="CASCADE")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsLike(): ?bool
    {
        return $this->is_like;
    }

    public function setIsLike(bool $is_like): self
    {
        $this->is_like = $is_like;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

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
