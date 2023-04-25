<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
  // Name of directory where I store all my image files
  const BLOC_IMAGE = 'bloc_img';

  // Definition of maximum file size that is allowed
  const MAX_FILE_SIZE = 1000000;


  public function __construct(
    private string $targetDirectory,
    private RequestStackContext $requestStackContext
  ){}

  public function upload(UploadedFile $file)
  {
    // if file > 1mo then thow exception
    if ($file->getSize() > self::MAX_FILE_SIZE) {
      throw new FileException('Le fichier est trop grand (taille maximale : 1Mo).');
    }

    $destination = $this->getTargetDirectory();
    $fileName = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
    try {
      $file->move($destination, $fileName);
    } catch (FileException $e) {
      // handle exception if something happens during file upload
      throw new FileException('Une erreur s\'est produite lors du téléversement de votre fichier : ' . $e->getMessage());
    }

    return $fileName;
  }

  public function getTargetDirectory()
  {
    return $this->targetDirectory . '/' . self::BLOC_IMAGE;
  }

  public function getPublicPath(string $path): string
  {
    return $this->requestStackContext
      ->getBasePath() . '/uploads/' . $path;
  }
}
