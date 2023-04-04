<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\CapsuleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\String\Slugger\SluggerInterface;
use Gedmo\Mapping\Annotation\Slug;

#[ORM\Entity(repositoryClass: CapsuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Capsule
{
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SlugTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 100)]
  private ?string $title = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(length: 150, nullable: true)]
  private ?string $background = null;

  #[ORM\Column]
  private ?bool $open = null;

  #[ORM\Column(type: Types::BOOLEAN, nullable:true)]
  private ?bool $explore = null;

  #[ORM\Column]
  private ?bool $collaboration = null;

  #[ORM\Column(length: 10)]
  #[Choice(
    choices: ['sealed', 'open'],
    message: 'Choose a valid status.',
  )]
  private ?string $status = null;

  #[ORM\OneToMany(mappedBy: 'capsule', targetEntity: Bloc::class, orphanRemoval: false)]
  private Collection $blocs;

  #[ORM\ManyToOne(inversedBy: 'capsules')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  private ?User $user = null;

  #[ORM\Column(length: 150, unique: true)]
  #[Slug(fields: ['title'])]
  private ?string $slug = null;

  #[ORM\OneToMany(mappedBy: 'capsule', targetEntity: Connection::class, orphanRemoval: false)]
  private Collection $connections;

  public function __construct()
  {
    $this->blocs = new ArrayCollection();
    $this->created_at = new \DateTimeImmutable();
    $this->updated_at = $this->created_at;
    $this->collaboration = 0;
    $this->connections = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }

  public function getBackground(): ?string
  {
    return $this->background;
  }

  public function setBackground(?string $background): self
  {
    $this->background = $background;

    return $this;
  }

  public function isOpen(): ?bool
  {
    return $this->open;
  }

  public function setOpen(bool $open): self
  {
    $this->open = $open;

    return $this;
  }

  public function isCollaboration(): ?bool
  {
    return $this->collaboration;
  }

  public function setCollaboration(bool $collaboration): self
  {
    $this->collaboration = $collaboration;

    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->status;
  }

  public function setStatus(string $status): self
  {
    $this->status = $status;
    $this->status === 'sealed' ? $this->setOpen(0) : $this->setOpen(1);

    return $this;
  }

  /**
   * @return Collection<int, Bloc>
   */
  public function getBlocs(): Collection
  {
    return $this->blocs;
  }

  public function addBloc(Bloc $bloc): self
  {
    if (!$this->blocs->contains($bloc)) {
      $this->blocs->add($bloc);
      $bloc->setCapsule($this);
    }

    return $this;
  }

  public function removeBloc(Bloc $bloc): self
  {
    if ($this->blocs->removeElement($bloc)) {
      // set the owning side to null (unless already changed)
      if ($bloc->getCapsule() === $this) {
        $bloc->setCapsule(null);
      }
    }

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  /**
   * @return Collection<int, Connection>
   */
  public function getConnections(): Collection
  {
      return $this->connections;
  }

  public function addConnection(Connection $connection): self
  {
      if (!$this->connections->contains($connection)) {
          $this->connections->add($connection);
          $connection->setCapsule($this);
      }

      return $this;
  }

  public function removeConnection(Connection $connection): self
  {
      if ($this->connections->removeElement($connection)) {
          // set the owning side to null (unless already changed)
          if ($connection->getCapsule() === $this) {
              $connection->setCapsule(null);
          }
      }

      return $this;
  }

  public function getExplore()
  {
    return $this->explore;
  }

  public function setExplore(bool $explore)
  {
    $this->explore = $explore;

    return $this;
  }

}
