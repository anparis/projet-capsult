<?php

namespace App\Controller;

use DateTime;
use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\Image;
use App\Service\Validation;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(BlocRepository $bl): Response
  {
    return new Response('HOME');
  }

  
}
