<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Cet email est déjà enregistré en base.")
 * @UniqueEntity(fields="username", message="Cet identifiant est déjà enregistré en base")
 */
class User implements UserInterface
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(max=25)
     */
    private $username = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(max=60)
     * @Assert\Email
     */
    private $email = '';

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @var string
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(max=4096)
     */
    private $plainPassword = '';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $passwordRequestedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $csrfToken;

    public function __construct()
    {
        $this->active = true;
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
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

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getSalt()
    {
        // pas besoin de salt puisque nous allons utiliser bcrypt
        // attention si vous utilisez une méthode d'encodage différente !
        // il faudra décommenter les lignes concernant le salt, créer la propriété correspondante, et renvoyer sa valeur dans cette méthode
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        if (!\in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        foreach ($roles as $role) {
            if ('ROLE_' !== \substr($role, 0, 5)) {
                throw new InvalidArgumentException("Chaque rôle doit commencer par 'ROLE_'");
            }
        }
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;
    }

    public function getPasswordRequestedAt(): \DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt($passwordRequestedAt): self
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

    public function getCsrfToken(): ?string
    {
        return $this->csrfToken;
    }

    public function setCsrfToken(string $csrfToken): void
    {
        $this->csrfToken = $csrfToken;
    }
}
