<?php

namespace App\Entity;

use App\Repository\HashtagpcRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HashtagpcRepository::class)]
class Hashtagpc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'hashtagpcs')]
    private ?Comment $comment = null;

    #[ORM\ManyToOne(inversedBy: 'hashtagpcs')]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'hashtagpcs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hashtag $hashtag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

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
}
