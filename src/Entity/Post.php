<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $timing = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private Collection $comments;

    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $likes;

    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'post')]
    private Collection $reports;

    #[ORM\OneToMany(targetEntity: Hashtagpc::class, mappedBy: 'post')]
    private Collection $hashtagpcs;

    #[ORM\OneToMany(targetEntity: ImagePost::class, mappedBy: 'post')]
    private $imagePosts;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->hashtagpcs = new ArrayCollection();
        $this->imagePosts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
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

    public function getTiming(): ?\DateTimeInterface
    {
        return $this->timing;
    }

    public function setTiming(\DateTimeInterface $timing): static
    {
        $this->timing = $timing;

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
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setPost($this);
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
            $hashtagpc->setPost($this);
        }

        return $this;
    }

    public function removeHashtagpc(Hashtagpc $hashtagpc): static
    {
        if ($this->hashtagpcs->removeElement($hashtagpc)) {
            // set the owning side to null (unless already changed)
            if ($hashtagpc->getPost() === $this) {
                $hashtagpc->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ImagePost[]
     */
    public function getImagePosts(): Collection
    {
        return $this->imagePosts ?? new ArrayCollection();
    }

    public function addImagePost(ImagePost $imagePost): self
    {
        if (!$this->imagePosts->contains($imagePost)) {
            $this->imagePosts[] = $imagePost;
            $imagePost->setPost($this);
        }

        return $this;
    }

    public function removeImagePost(ImagePost $imagePost): self
    {
        if ($this->imagePosts->removeElement($imagePost)) {
            // set the owning side to null (unless already changed)
            if ($imagePost->getPost() === $this) {
                $imagePost->setPost(null);
            }
        }

        return $this;
    }
}
