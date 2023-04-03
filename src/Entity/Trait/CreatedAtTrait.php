<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait CreatedAtTrait
{
  #[ORM\Column(type: 'datetime_immutable')]
  private ?\DateTimeImmutable $created_at = null;

  public function getCreatedAt(): string
  {
    return $this->created_at->format('d/m/Y');
  }

  public function setCreatedAt(\DateTimeImmutable $created_at): self
  {
    $this->created_at = $created_at;

    return $this;
  }
}