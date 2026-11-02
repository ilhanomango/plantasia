<?php

namespace App\Entity;

use App\Repository\PlantesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlantesRepository::class)]
class Plantes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text')]
    private ?string $image = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    /**
     * @var Collection<int, UserPlante>
     */
    #[ORM\OneToMany(targetEntity: UserPlante::class, mappedBy: 'plante', cascade: ['remove'], orphanRemoval: true)]
    private Collection $userPlantes;

    public function __construct()
    {
        $this->userPlantes = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, UserPlante>
     */
    public function getUserPlantes(): Collection
    {
        return $this->userPlantes;
    }

    public function addUserPlante(UserPlante $userPlante): static
    {
        if (!$this->userPlantes->contains($userPlante)) {
            $this->userPlantes->add($userPlante);
            $userPlante->setPlante($this);
        }

        return $this;
    }

    public function removeUserPlante(UserPlante $userPlante): static
    {
        if ($this->userPlantes->removeElement($userPlante)) {
            // set the owning side to null (unless already changed)
            if ($userPlante->getPlante() === $this) {
                $userPlante->setPlante(null);
            }
        }

        return $this;
    }
}
