<?php

namespace App\Entity;

use App\Repository\PostsReportsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ReportReasons;
use App\Entity\Posts;
use App\Entity\User;

#[ORM\Entity(repositoryClass: PostsReportsRepository::class)]
class PostsReports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: 'id')]
    #[ORM\Column(type: 'integer')]
    private $post;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'id')]
    #[ORM\Column(type: 'integer')]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $date;

    #[ORM\ManyToOne(targetEntity: ReportReasons::class, inversedBy: 'id')]
    #[ORM\Column(type: 'integer')]
    private $reason;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?int
    {
        return $this->post;
    }

    public function setPostId($post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user;
    }

    public function setUserId($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReasonId(): ?int
    {
        return $this->reason;
    }

    public function setReasonId($reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
