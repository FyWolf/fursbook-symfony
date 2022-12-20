<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Posts;
use App\Entity\User;

#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Posts::class)]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private $post_id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function setPostId($post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId($user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
