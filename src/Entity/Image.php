<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BLOB)]
    private $image;

    #[ORM\OneToMany(targetEntity: ImagePost::class, mappedBy: 'image', orphanRemoval: true)]
    private Collection $imagePosts;

    public function __construct()
    {
        $this->imagePosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, ImagePost>
     */
    public function getImagePosts(): Collection
    {
        return $this->imagePosts;
    }

    public function addImagePost(ImagePost $imagePost): static
    {
        if (!$this->imagePosts->contains($imagePost)) {
            $this->imagePosts->add($imagePost);
            $imagePost->setImage($this);
        }

        return $this;
    }

    public function removeImagePost(ImagePost $imagePost): static
    {
        if ($this->imagePosts->removeElement($imagePost)) {
            // set the owning side to null (unless already changed)
            if ($imagePost->getImage() === $this) {
                $imagePost->setImage(null);
            }
        }

        return $this;
    }
}
