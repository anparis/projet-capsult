<?php

namespace App\Entity;

use App\Service\FileUploader;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $nom_fichier = null;

    #[ORM\Column(length: 10)]
    private ?string $type_fichier = null;

    #[ORM\OneToOne(inversedBy: 'image', cascade: ['persist', 'remove'])]
    private ?Bloc $bloc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFichier(): ?string
    {
        return FileUploader::BLOC_IMAGE.'/'.$this->nom_fichier;
    }

    public function setNomFichier(string $nom_fichier): self
    {
        $this->nom_fichier = $nom_fichier;

        return $this;
    }

    public function getTypeFichier(): ?string
    {
        return $this->type_fichier;
    }

    public function setTypeFichier(string $type_fichier): self
    {
        $this->type_fichier = $type_fichier;

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
            $this->bloc->setImage(null);
        }

        // set the owning side of the relation if necessary
        if ($bloc !== null && $bloc->getImage() !== $this) {
            $bloc->setImage($this);
        }

        $this->bloc = $bloc;

        return $this;
    }

    // public function __toString()
    // {
    //   return "Image publié par ".$this->bloc->n
    // }
}
