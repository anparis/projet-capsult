<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validation
{
  /**
   * Validate an url input from user
   * Return false if number of violation>0
   */
  public function validateUrl(string $txt, ValidatorInterface $validator): bool
  {
    $input = ['url' => $txt];

    $constraints = new Collection([
      'url' => [new Url(), new NotBlank()],
    ]);

    $violation = $validator->validate($input, $constraints);

    return count($violation) > 0 ? 0 : 1;
  }

  /**
   * Check if user upload is a valid Image 
   * Return false if number of violation>0
   */
  public function validateImage(UploadedFile $img, ValidatorInterface $validator): bool
  {
    $input = ['img' => $img];

    $constraints = new Collection([
      'img' => [new Image(), new NotBlank()],
    ]);

    $violation = $validator->validate($input, $constraints);
    return count($violation) > 0 ? 0 : 1;
  }

  /* Validate a text input
   * Return false if number of violation>0
  */
  public function validateText(string $txt, ValidatorInterface $validator): bool
  {
    $input = ['txt' => $txt];

    $constraints = new Collection([
      'txt' => [new Assert\Type('string'), new NotBlank()],
    ]);

    $violation = $validator->validate($input, $constraints);

    return count($violation) > 0 ? 0 : 1;
  }
}
