<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Gedmo\Mapping\Annotation\Slug;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  use SlugTrait;
  
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  private ?string $email = null;

  #[ORM\Column(length: 50)]
  private ?string $username = null;

  #[ORM\Column]
  private array $roles = [];

  #[ORM\Column(length: 150, unique: true)]
  #[Slug(fields: ['username'])]
  private ?string $slug = null;

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\OneToMany(mappedBy: 'user', targetEntity: Capsule::class)]
  private Collection $capsules;

  #[ORM\ManyToMany(targetEntity: Capsule::class, mappedBy: 'likes')]
  private Collection $capsules_liked;

  #[ORM\ManyToMany(targetEntity: Capsule::class, mappedBy: 'collaborators')]
  private Collection $capsules_collabs;

  public function __construct()
  {
    $this->capsules = new ArrayCollection();
    $this->capsules_liked = new ArrayCollection();
    $this->capsules_collabs = new ArrayCollection();
  }

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

  public function getUsername()
  {
    return $this->username;
  }

  public function setUsername($username)
  {
    $this->username = $username;

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

  public function setRoles(array $roles): self
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

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials()
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  /**
   * @return Collection<int, Capsule>
   */
  public function getCapsules(): Collection
  {
    return $this->capsules;
  }

  public function addCapsule(Capsule $capsule): self
  {
    if (!$this->capsules->contains($capsule)) {
      $this->capsules->add($capsule);
      $capsule->setUser($this);
    }

    return $this;
  }

  public function removeCapsule(Capsule $capsule): self
  {
    if ($this->capsules->removeElement($capsule)) {
      // set the owning side to null (unless already changed)
      if ($capsule->getUser() === $this) {
        $capsule->setUser(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Capsule>
   */
  public function getCapsulesLiked(): Collection
  {
      return $this->capsules_liked;
  }

  public function addCapsuleLiked(Capsule $capsule): self
  {
    if (!$this->capsules_liked->contains($capsule)) {
      $capsule->addLike($this);
      $this->capsules_liked->add($capsule);
    }
    return $this;
  }

  /**
   * @return Collection<int, Capsule>
   */
  public function getCapsulesCollabs(): Collection
  {
      return $this->capsules_collabs;
  }

  public function addCapsulesCollab(Capsule $capsulesCollab): self
  {
      if (!$this->capsules_collabs->contains($capsulesCollab)) {
          $this->capsules_collabs->add($capsulesCollab);
          $capsulesCollab->addCollaborator($this);
      }

      return $this;
  }

  public function removeCapsulesCollab(Capsule $capsulesCollab): self
  {
      if ($this->capsules_collabs->removeElement($capsulesCollab)) {
          $capsulesCollab->removeCollaborator($this);
      }

      return $this;
  }
}
