<?php

namespace App\Entity;

use App\Repository\ProfileReportsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ReportReasons;
use App\Entity\User;

#[ORM\Entity(repositoryClass: ProfileReportsRepository::class)]
class ProfileReports
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "id")]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private $profile;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $date;

    #[ORM\ManyToOne(targetEntity: ReportReasons::class, inversedBy: "id")]
    #[ORM\Column(type: 'integer')]
    private $reason;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfileId(): ?int
    {
        return $this->profile;
    }

    public function setProfileId($profile)
    {
        $this->profile = $profile;

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

    public function setUserId($user)
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

    public function setReasonId(int $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
