<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WeatherData::class, orphanRemoval: true)]
    private Collection $weatherData;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private array $savedCities = [];

    public function __construct()
    {
        $this->weatherData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // public static function createFromPayload($username, array $payload)
    // {
    //     return new self(
    //         $username,
    //         $payload['roles'],
    //         $payload['email']
    //     );
    // }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, WeatherData>
     */
    public function getWeatherData(): Collection
    {
        return $this->weatherData;
    }

    public function addWeatherData(WeatherData $weatherData): static
    {
        if (!$this->weatherData->contains($weatherData)) {
            $this->weatherData->add($weatherData);
            $weatherData->setUser($this);
        }

        return $this;
    }

    public function removeWeatherData(WeatherData $weatherData): static
    {
        if ($this->weatherData->removeElement($weatherData)) {
            // set the owning side to null (unless already changed)
            if ($weatherData->getUser() === $this) {
                $weatherData->setUser(null);
            }
        }

        return $this;
    }

    public function setSavedCities(array $savedCities): static
    {
        $this->savedCities = $savedCities;

        return $this;
    }

    public function getSavedCities(): array
    {
        return array_unique($this->savedCities);
    }

    public function addSavedCity(string $savedCity): static
    {
        $this->savedCities[] = $savedCity;

        return $this;
    }

    public function removeSavedCity(string $savedCity): static
    {
        if (in_array($savedCity, $this->savedCities)) {
            $key = array_search($savedCity, $this->savedCities);
            unset($this->savedCities[$key]);
        }

        return $this;
    }
}
