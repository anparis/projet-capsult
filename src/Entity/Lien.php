<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LienRepository;

#[ORM\Entity(repositoryClass: LienRepository::class)]
class Lien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $thumb = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $html = null;
    
    #[ORM\OneToOne(inversedBy: 'lien', cascade: ['persist', 'remove'])]
    private ?Bloc $bloc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(string $thumb): self
    {
        $this->thumb = $thumb;

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
            $this->bloc->setLien(null);
        }

        // set the owning side of the relation if necessary
        if ($bloc !== null && $bloc->getLien() !== $this) {
            $bloc->setLien($this);
        }

        $this->bloc = $bloc;

        return $this;
    }

    public function getHtml()
    {
        return $this->html;
    }


    public function setHtml($html):self
    {
        $this->html = $html;

        return $this;
    }
}
