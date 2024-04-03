<?php

namespace App\Entity;

use App\Repository\HashtagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HashtagRepository::class)]
class Hashtag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Follow::class, mappedBy: 'hashtag')]
    private Collection $follows;

    #[ORM\OneToMany(targetEntity: Hashtagpc::class, mappedBy: 'hashtag')]
    private Collection $hashtagpcs;

    public function __construct()
    {
        $this->follows = new ArrayCollection();
        $this->hashtagpcs = new ArrayCollection();
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
            $follow->setHashtag($this);
        }

        return $this;
    }

    public function removeFollow(Follow $follow): static
    {
        if ($this->follows->removeElement($follow)) {
            // set the owning side to null (unless already changed)
            if ($follow->getHashtag() === $this) {
                $follow->setHashtag(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hashtagpc>
     */
    public function getHashtagpcs(): Collection
    {
        return $this->hashtagpcs;
    }

    public function addHashtagpc(Hashtagpc $hashtagpc): static
    {
        if (!$this->hashtagpcs->contains($hashtagpc)) {
            $this->hashtagpcs->add($hashtagpc);
            $hashtagpc->setHashtag($this);
        }

        return $this;
    }

    public function removeHashtagpc(Hashtagpc $hashtagpc): static
    {
        if ($this->hashtagpcs->removeElement($hashtagpc)) {
            // set the owning side to null (unless already changed)
            if ($hashtagpc->getHashtag() === $this) {
                $hashtagpc->setHashtag(null);
            }
        }

        return $this;
    }
}
