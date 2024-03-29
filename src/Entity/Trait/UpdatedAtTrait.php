<?php

namespace App\Entity\Trait;

use DateTimeImmutable;
use IntlDateFormatter;
use Doctrine\ORM\Mapping as ORM;

trait UpdatedAtTrait
{
  #[ORM\Column(type: 'datetime_immutable')]
  private ?\DateTimeImmutable $updated_at = null;

  public function getUpdatedAt(): \DateTimeImmutable
  {
    return $this->updated_at;
  }

  public function setUpdatedAt(\DateTimeImmutable $updated_at): self
  {
    $this->updated_at = $updated_at;

    return $this;
  }

  public function getUpdatedAtFormatted(): string
  {
    $fmt = new IntlDateFormatter(
      'fr_FR',
      IntlDateFormatter::FULL,
      IntlDateFormatter::SHORT,
      'Europe/Paris',
      IntlDateFormatter::GREGORIAN
  );
  
    return $fmt->format($this->updated_at);
  }


  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function updateDate(): void
  {
    $dateTimeNow = new DateTimeImmutable('now');
    $this->setUpdatedAt($dateTimeNow);
  }
}
