<?php

namespace App\Entity;

use App\Repository\PostsReportsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ReportReasons;
use App\Entity\Posts;

#[ORM\Entity(repositoryClass: PostsReportsRepository::class)]
class PostsReports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: "id")]
    private $postId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $desctription;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "id")]
    private $userId;

    #[ORM\Column(type: 'string', length: 255)]
    private $date;

    #[ORM\ManyToOne(targetEntity: ReportReasons::class, inversedBy: "id")]
    private $reasonId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;

        return $this;
    }

    public function getDesctription(): ?string
    {
        return $this->desctription;
    }

    public function setDesctription(?string $desctription): self
    {
        $this->desctription = $desctription;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

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
        return $this->reasonId;
    }

    public function setReasonId(int $reasonId): self
    {
        $this->reasonId = $reasonId;

        return $this;
    }
}
