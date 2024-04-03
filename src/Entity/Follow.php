<?php

namespace App\Entity;

use App\Repository\FollowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
class Follow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $following_user = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    private ?User $followed_user = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    private ?Institution $institution = null;

    #[ORM\ManyToOne(inversedBy: 'follows')]
    private ?Hashtag $hashtag = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFollowingUser(): ?User
    {
        return $this->following_user;
    }

    public function setFollowingUser(?User $following_user): static
    {
        $this->following_user = $following_user;

        return $this;
    }

    public function getFollowedUser(): ?User
    {
        return $this->followed_user;
    }

    public function setFollowedUser(?User $followed_user): static
    {
        $this->followed_user = $followed_user;

        return $this;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): static
    {
        $this->institution = $institution;

        return $this;
    }

    public function getHashtag(): ?Hashtag
    {
        return $this->hashtag;
    }

    public function setHashtag(?Hashtag $hashtag): static
    {
        $this->hashtag = $hashtag;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
