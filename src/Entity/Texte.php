<?php

namespace App\Entity;

use App\Repository\TexteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TexteRepository::class)]
class Texte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $texte = null;

    #[ORM\OneToOne(inversedBy: 'texte', cascade: ['persist', 'remove'])]
    private ?Bloc $bloc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getBloc(): ?Bloc
    {
        return $this->bloc;
    }

    public function setBloc(?Bloc $bloc): self
    {
        // unset the owning side of the relation if necessary
        if ($bloc === null && $this->bloc !== null) {
            $this->bloc->setTexte(null);
        }

        // set the owning side of the relation if necessary
        if ($bloc !== null && $bloc->getTexte() !== $this) {
            $bloc->setTexte($this);
        }

        $this->bloc = $bloc;

        return $this;
    }
}
