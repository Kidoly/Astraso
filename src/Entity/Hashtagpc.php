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
    private ?comment $comment = null;

    #[ORM\ManyToOne(inversedBy: 'hashtagpcs')]
    private ?post $post = null;

    #[ORM\ManyToOne(inversedBy: 'hashtagpcs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?hashtag $hashtag = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?comment
    {
        return $this->comment;
    }

    public function setComment(?comment $comment): static
    {
        $this->comment = $comment;

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

    public function getHashtag(): ?hashtag
    {
        return $this->hashtag;
    }

    public function setHashtag(?hashtag $hashtag): static
    {
        $this->hashtag = $hashtag;

        return $this;
    }
}
