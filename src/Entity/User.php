<?php

namespace SSO\FpBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use SSO\FpBundle\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, EquatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    private $logoutUser = false;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: "json")]
    private $roles = [];

    #[ORM\Column(type: "string", length: 32)]
    private $firstname;

    #[ORM\Column(type: "string", length: 32)]
    private $lastname;

    #[ORM\Column(type: "string", length: 1024)]
    private $accessToken;

    #[ORM\Column(type: "string", length: 1024)]
    private $refreshToken;

    #[ORM\Column(type: "boolean")]
    private $isBlockedByFp = false;

    #[ORM\Column(type: "datetime_immutable")]
    private $expiresIn;

    #[ORM\Column(type: "json", nullable: true)]
    private $extras;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function isBlockedByFp(): bool
    {
        return $this->isBlockedByFp;
    }

    public function setIsBlockedByFp(bool $isBlockedByFp): self
    {
        $this->isBlockedByFp = $isBlockedByFp;
        return $this;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $this->isBlockedByFp() === false && $this->logoutUser === false;
    }

    public function setToLogoutUser(bool $logout)
    {
        $this->logoutUser = $logout;
        return $this;
    }

    public function getExtras(): ?array
    {
        return $this->extras;
    }

    public function setExtras(array $extras): self
    {
        $this->extras = $extras;

        return $this;
    }

    public function getExpiresIn(): ?\DateTimeImmutable
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(\DateTimeImmutable $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }
}