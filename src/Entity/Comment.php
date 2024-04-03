<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?post $post = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(targetEntity: Hashtagpc::class, mappedBy: 'comment')]
    private Collection $hashtagpcs;

    public function __construct()
    {
        $this->hashtagpcs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getPost(): ?post
    {
        return $this->post;
    }

    public function setPost(?post $post): static
    {
        $this->post = $post;

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
            $hashtagpc->setComment($this);
        }

        return $this;
    }

    public function removeHashtagpc(Hashtagpc $hashtagpc): static
    {
        if ($this->hashtagpcs->removeElement($hashtagpc)) {
            // set the owning side to null (unless already changed)
            if ($hashtagpc->getComment() === $this) {
                $hashtagpc->setComment(null);
            }
        }

        return $this;
    }
}
