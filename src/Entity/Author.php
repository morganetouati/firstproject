<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Author.
 *
 * @ORM\Table(name="author")
 * @ORM\Entity
 */
class Author
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
     * @ORM\Column(name="lastname", type="string", length=255, unique=true)
     */
    private $lastname = '';

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title = '';

    /**
     * @var string
     * @ORM\Column(name="firstname", type="string", length=255, unique=true)
     */
    private $firstname = '';

    /**
     * @var string
     * @ORM\Column(name="short_bio", type="string", length=500)
     */
    private $shortBio = '';

    /**
     * @var string
     * @Assert\Length(min=10, max=20, minMessage="min_length", maxMessage="max_length")
     * @Assert\Regex(pattern="/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/")
     * @ORM\Column(name="phone", type="string", length=10, nullable=true)
     */
    private $phone = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\Length(max=60)
     * @Assert\Email
     */
    private $email = '';

    /**
     * @var string
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company = '';

    /**
     * @var string
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook = '';

    /**
     * @var string
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter = '';

    /**
     * @var string
     * @ORM\Column(name="github", type="string", length=255, nullable=true)
     */
    private $github = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function setLastName(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastname;
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

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setShortBio(string $shortBio): self
    {
        $this->shortBio = $shortBio;

        return $this;
    }

    public function getShortBio(): string
    {
        return $this->shortBio;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setFacebook(string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getFacebook(): string
    {
        return $this->facebook;
    }

    public function setTwitter(string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getTwitter(): string
    {
        return $this->twitter;
    }

    public function setGithub(string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getGithub(): string
    {
        return $this->github;
    }
}
