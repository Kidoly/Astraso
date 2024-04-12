<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_reporter = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?User $user_reported = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reason $reason = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserReporter(): ?user
    {
        return $this->user_reporter;
    }

    public function setUserReporter(?user $user_reporter): static
    {
        $this->user_reporter = $user_reporter;

        return $this;
    }

    public function getUserReported(): ?user
    {
        return $this->user_reported;
    }

    public function setUserReported(?user $user_reported): static
    {
        $this->user_reported = $user_reported;

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

    public function getReason(): ?reason
    {
        return $this->reason;
    }

    public function setReason(?reason $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
