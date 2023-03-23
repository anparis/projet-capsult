<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
  const BLOC_IMAGE = 'bloc_img';
  private string $targetDirectory;
  private RequestStackContext $requestStackContext;

  public function __construct($targetDirectory, RequestStackContext $requestStackContext)
  {
    $this->targetDirectory = $targetDirectory;
    $this->requestStackContext = $requestStackContext;
  }

  public function upload(UploadedFile $file)
  {
    // $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $destination = $this->getTargetDirectory();
    $fileName = bin2hex(random_bytes(6)) . '.' . $file->guessExtension();
    try {
      $file->move($destination, $fileName);
    } catch (FileException $e) {
      // ... handle exception if something happens during file upload
      dump('file upload error');
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
