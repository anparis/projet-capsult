<?php

namespace App\Entity;

use App\Repository\ConnectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectionRepository::class)]
class Connection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'connections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Capsule $capsule = null;

    #[ORM\ManyToOne(inversedBy: 'connections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bloc $bloc = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $connected_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConnectedAt(): ?\DateTimeImmutable
    {
        return $this->connected_at;
    }

    public function setConnectedAt(\DateTimeImmutable $connected_at): self
    {
        $this->connected_at = $connected_at;

        return $this;
    }
}
