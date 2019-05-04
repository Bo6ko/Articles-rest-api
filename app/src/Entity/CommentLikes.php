<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentLikesRepository")
 */
class CommentLikes
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
    private $comment_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentId(): ?string
    {
        return $this->comment_id;
    }

    public function setCommentId(string $comment_id): self
    {
        $this->comment_id = $comment_id;
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
