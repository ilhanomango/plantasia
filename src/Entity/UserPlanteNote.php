<?php

namespace App\Entity;

use App\Repository\UserPlanteNoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPlanteNoteRepository::class)]
class UserPlanteNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'userPlanteNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserPlante $userPlante = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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
