<?php

namespace App\Entity;

use App\Repository\UserPlantePhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPlantePhotoRepository::class)]
class UserPlantePhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'userPlantePhotos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserPlante $userPlante = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserPlante(): ?UserPlante
    {
        return $this->userPlante;
    }

    public function setUserPlante(?UserPlante $userPlante): static
    {
        $this->userPlante = $userPlante;

        return $this;
    }
}
