<?php

namespace App\Entity;

use App\Entity\Trait\CreatedAtTrait;
use App\Repository\ConnectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
class Connection
{
  use CreatedAtTrait;

  #[ORM\Id]
  #[ORM\ManyToOne(inversedBy: 'connections')]
  #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
  private ?Capsule $capsule = null;

  #[ORM\Id]
  #[ORM\ManyToOne(inversedBy: 'connections')]
  #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
  private ?Bloc $bloc = null;

  public function __construct()
  {
    $this->created_at = new \DateTimeImmutable();
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

  public function getBloc(): ?Bloc
  {
    return $this->bloc;
  }

  public function setBloc(?Bloc $bloc): self
  {
    $this->bloc = $bloc;

    return $this;
  }
}
