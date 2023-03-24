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
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: CapsuleRepository::class)]
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

  #[ORM\Column]
  private ?bool $collaboration = null;

  #[ORM\Column(length: 10)]
  #[Choice(
    choices: ['sealed', 'open'],
    message: 'Choose a valid status.',
  )]
  private ?string $status = null;

  #[ORM\OneToMany(mappedBy: 'capsule', targetEntity: Bloc::class, orphanRemoval: true)]
  private Collection $blocs;

  #[ORM\ManyToOne(inversedBy: 'capsules')]
  #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
  private ?User $user = null;

  public function __construct()
  {
    $this->blocs = new ArrayCollection();
    $this->created_at = new \DateTimeImmutable();
    $this->updated_at = $this->created_at;
    $this->collaboration = 0;
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
}
