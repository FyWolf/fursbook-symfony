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
    private $profileId;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

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

    public function getProfileId(): ?int
    {
        return $this->profileId;
    }

    public function setProfileId(int $profileId): self
    {
        $this->profileId = $profileId;

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
