<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="article")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Article
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title = '';
    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug = '';
    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=2000)
     */
    private $description = '';
    /**
     * @var string
     * @ORM\Column(name="body", type="text")
     */
    private $body = '';
    /**
     * @var Author|null
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;
    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetimetz")
     */
    private $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;
    /**
     * @var string
     * @Assert\File(mimeTypes={"image/jpeg", "image/png"})
     * @ORM\Column(name="imgUploaded", type="string")
     */
    private $imgUploaded;

    public function getId(): int
    {
        return $this->id;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setImgUploaded($imgUploaded): self
    {
        $this->imgUploaded = $imgUploaded;

        return $this;
    }

    public function getImgUploaded()
    {
        return $this->imgUploaded;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(): void
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
