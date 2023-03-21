<?php

namespace App\Entity;

use ORM\Embeddable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BlocRepository;
use Doctrine\ORM\Mapping\Embedded;

#[ORM\Entity(repositoryClass: BlocRepository::class)]
class Bloc
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 100, nullable: true)]
  private ?string $titre = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $date_creation = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $date_modification = null;

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
  #[ORM\JoinColumn(nullable: false)]
  private ?Capsule $capsule = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitre(): ?string
  {
    return $this->titre;
  }

  public function setTitre(?string $titre): self
  {
    $this->titre = $titre;

    return $this;
  }

  public function getDateCreation(): ?\DateTimeInterface
  {
    return $this->date_creation;
  }

  public function setDateCreation(\DateTimeInterface $date_creation): self
  {
    $this->date_creation = $date_creation;

    return $this;
  }

  public function getDateModification(): ?\DateTimeInterface
  {
    return $this->date_modification;
  }

  public function setDateModification(\DateTimeInterface $date_modification): self
  {
    $this->date_modification = $date_modification;

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
}
