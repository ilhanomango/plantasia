<?php

namespace App\Entity;

use App\Repository\UserPlanteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPlanteRepository::class)]
class UserPlante
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userPlantes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userPlantes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Plantes $plante = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dernierArrosage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surnom = null;

    #[ORM\Column(nullable: true)]
    private ?int $frequenceArrosage = null;

    public function getFrequenceArrosage(): ?int
    {
        return $this->frequenceArrosage;
    }

    public function setFrequenceArrosage(?int $frequenceArrosage): static
    {
        $this->frequenceArrosage = $frequenceArrosage;

        return $this;
    }

    /**
     * Calcule le nombre de jours restants avant le prochain arrosage
     */
    public function getJoursRestants(): ?int
    {
        if (!$this->dernierArrosage || !$this->frequenceArrosage) {
            return null;
        }

        $prochainArrosage = (clone $this->dernierArrosage)->modify("+{$this->frequenceArrosage} days");
        $aujourdhui = new \DateTime('today');
        $dateProchain = (clone $prochainArrosage)->setTime(0, 0);

        $diff = $aujourdhui->diff($dateProchain);

        return $diff->invert ? -$diff->days : $diff->days;
    }

    /**
     * @var Collection<int, UserPlantePhoto>
     */
    #[ORM\OneToMany(targetEntity: UserPlantePhoto::class, mappedBy: 'userPlante', orphanRemoval: true)]
    private Collection $userPlantePhotos;

    /**
     * @var Collection<int, UserPlanteNote>
     */
    #[ORM\OneToMany(targetEntity: UserPlanteNote::class, mappedBy: 'userPlante', orphanRemoval: true)]
    private Collection $userPlanteNotes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emplacement = null;

    public function __construct()
    {
        $this->userPlantePhotos = new ArrayCollection();
        $this->userPlanteNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPlante(): ?Plantes
    {
        return $this->plante;
    }

    public function setPlante(?Plantes $plante): static
    {
        $this->plante = $plante;

        return $this;
    }

    public function getDernierArrosage(): ?\DateTimeImmutable
    {
        return $this->dernierArrosage;
    }

    public function setDernierArrosage(?\DateTimeImmutable $dernierArrosage): static
    {
        $this->dernierArrosage = $dernierArrosage;

        return $this;
    }

    public function getSurnom(): ?string
    {
        return $this->surnom;
    }

    public function setSurnom(?string $surnom): static
    {
        $this->surnom = $surnom;

        return $this;
    }

    /**
     * @return Collection<int, UserPlantePhoto>
     */
    public function getUserPlantePhotos(): Collection
    {
        return $this->userPlantePhotos;
    }

    public function addUserPlantePhoto(UserPlantePhoto $userPlantePhoto): static
    {
        if (!$this->userPlantePhotos->contains($userPlantePhoto)) {
            $this->userPlantePhotos->add($userPlantePhoto);
            $userPlantePhoto->setUserPlante($this);
        }

        return $this;
    }

    public function removeUserPlantePhoto(UserPlantePhoto $userPlantePhoto): static
    {
        if ($this->userPlantePhotos->removeElement($userPlantePhoto)) {
            // set the owning side to null (unless already changed)
            if ($userPlantePhoto->getUserPlante() === $this) {
                $userPlantePhoto->setUserPlante(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserPlanteNote>
     */
    public function getUserPlanteNotes(): Collection
    {
        return $this->userPlanteNotes;
    }

    public function addUserPlanteNote(UserPlanteNote $userPlanteNote): static
    {
        if (!$this->userPlanteNotes->contains($userPlanteNote)) {
            $this->userPlanteNotes->add($userPlanteNote);
            $userPlanteNote->setUserPlante($this);
        }

        return $this;
    }

    public function removeUserPlanteNote(UserPlanteNote $userPlanteNote): static
    {
        if ($this->userPlanteNotes->removeElement($userPlanteNote)) {
            // set the owning side to null (unless already changed)
            if ($userPlanteNote->getUserPlante() === $this) {
                $userPlanteNote->setUserPlante(null);
            }
        }

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(?string $emplacement): static
    {
        $this->emplacement = $emplacement;

        return $this;
    }
}
