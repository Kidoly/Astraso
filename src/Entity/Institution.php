<?php

namespace App\Entity;

use App\Repository\InstitutionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstitutionRepository::class)]
class Institution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $postal_code = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private ?bool $accepted = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'institution')]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: Follow::class, mappedBy: 'institution', orphanRemoval: true)]
    private Collection $follows;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $Chef = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->follows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postal_code;
    }

    public function setPostalCode(int $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getFollows(): Collection
    {
        return $this->follows;
    }

    public function addFollow(Follow $follow): static
    {
        if (!$this->follows->contains($follow)) {
            $this->follows->add($follow);
            $follow->setInstitution($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): static
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getInstitution() === $this) {
                $follow->setInstitution(null);
            }
        }

        return $this;
    }

    public function getChef(): ?User
    {
        return $this->Chef;
    }

    public function setChef(?User $Chef): static
    {
        $this->Chef = $Chef;

        return $this;
    }
}
