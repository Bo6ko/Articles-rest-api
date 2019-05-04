<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleLikesRepository")
 */
class ArticleLikes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $article_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleId(): ?string
    {
        return $this->article_id;
    }

    public function setArticleId(string $article_id): self
    {
        $this->article_id = $article_id;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $userId): self
    {
        $this->user_id = $userId;
        return $this;
    }
}
