<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private $Owner;

    #[ORM\Column(type: 'string', length: 300, nullable: true)]
    private $content;

    #[ORM\Column(type: 'string', length: 1)]
    private $nbPictures;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $picture1;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $picture2;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $picture3;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $picture4;

    #[ORM\Column(type: 'string', length: 255)]
    private $datePosted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->Owner;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }


    public function setOwner($Owner): self
    {
        $this->Owner = $Owner;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDateCreated(): ?string
    {
        return $this->datePosted;
    }

    public function setDateCreated(?string $date): self
    {
        $this->datePosted = $date;

        return $this;
    }

    public function getNbPictures(): ?string
    {
        return $this->nbPictures;
    }

    public function setNbPictures(string $nbPictures): self
    {
        $this->nbPictures = $nbPictures;

        return $this;
    }

    public function getPicture1(): ?string
    {
        return $this->picture1;
    }

    public function setPicture1(?string $picture1): self
    {
        $this->picture1 = $picture1;

        return $this;
    }

    public function getPicture2(): ?string
    {
        return $this->picture2;
    }

    public function setPicture2(?string $picture2): self
    {
        $this->picture2 = $picture2;

        return $this;
    }

    public function getPicture3(): ?string
    {
        return $this->picture3;
    }

    public function setPicture3(?string $picture3): self
    {
        $this->picture3 = $picture3;

        return $this;
    }

    public function getPicture4(): ?string
    {
        return $this->picture4;
    }

    public function setPicture4(?string $picture4): self
    {
        $this->picture4 = $picture4;

        return $this;
    }

    public function getDatePosted(): ?string
    {
        return $this->datePosted;
    }

    public function setDatePosted(string $datePosted): self
    {
        $this->datePosted = $datePosted;

        return $this;
    }
}
