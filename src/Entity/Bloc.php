<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\SlugTrait;
use App\Entity\Trait\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ORM\Embeddable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BlocRepository;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: BlocRepository::class)]
class Bloc
{
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SlugTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 100, nullable: true)]
  private ?string $title = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $contenu = null;

  #[ORM\Column(length: 20)]
  private ?string $type = null;

  #[ORM\OneToOne(mappedBy: 'bloc', cascade: ['persist', 'remove'])]
  private ?Lien $lien = null;

  #[ORM\OneToOne(mappedBy: 'bloc', cascade: ['persist', 'remove'])]
  private ?Image $image = null;

  #[ORM\ManyToOne(inversedBy: 'blocs')]
  #[ORM\JoinColumn(nullable: false, onDelete:'CASCADE')]
  private ?Capsule $capsule = null;

  #[ORM\OneToMany(mappedBy: 'bloc', targetEntity: Connection::class, orphanRemoval: true)]
  private Collection $connections;

  public function __construct()
  {
    $this->created_at = new \DateTimeImmutable();
    $this->updated_at = $this->created_at;
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

  public function setTitle(?string $title): self
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

  public function getContenu(): ?string
  {
    return $this->contenu;
  }

  public function setContenu(?string $contenu): self
  {
    $this->contenu = $contenu;

    return $this;
  }

  public function getType(): ?string
  {
    return $this->type;
  }

  public function setType(?string $type): self
  {
    $this->type = $type;

    return $this;
  }

  public function getLien(): ?Lien
  {
    return $this->lien;
  }

  public function setLien(?Lien $lien): self
  {
    $this->lien = $lien;

    return $this;
  }

  public function getImage(): ?Image
  {
    return $this->image;
  }

  public function setImage(?Image $image): self
  {
    $this->image = $image;

    return $this;
  }

  public function getCapsule(): ?Capsule
  {
      return $this->capsule;
  }

  public function setCapsule(?Capsule $capsule): self
  {
      $this->capsule = $capsule;

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
          $connection->setBloc($this);
      }

      return $this;
  }

  public function removeConnection(Connection $connection): self
  {
      if ($this->connections->removeElement($connection)) {
          // set the owning side to null (unless already changed)
          if ($connection->getBloc() === $this) {
              $connection->setBloc(null);
          }
      }

      return $this;
  }
}
