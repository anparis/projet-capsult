<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validation
{
  /**
   * Validate an url input from user
   * Return true if number of violation>0
   */
  public function validateUrl(string $txt, ValidatorInterface $validator): bool
  {
    $input = ['url' => $txt];

    $constraints = new Collection([
      'url' => [new Url(), new NotBlank()],
    ]);

    $violation = $validator->validate($input, $constraints);

    return count($violation) > 0 ? 1 : 0;
  }

  /**
   * Validate an url input from user
   * Return true if number of violation>0
   */
  public function validateImage(UploadedFile $img, ValidatorInterface $validator): bool
  {
    $input = ['img' => $img];

    dump($input);
    $constraints = new Collection([
      'img' => [new Image(), new NotBlank()],
    ]);

    $violation = $validator->validate($input, $constraints);
    dd($violation);
    return count($violation) > 0 ? 1 : 0;
  }
}
