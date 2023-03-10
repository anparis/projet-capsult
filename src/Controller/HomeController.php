<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Bloc;
use App\Entity\Image;
use App\Entity\Lien;
use App\Entity\Texte;
use App\Service\Validation;
use App\Service\FileUploader;
use App\Repository\BlocRepository;
use App\Repository\ImageRepository;
use App\Repository\LienRepository;
use App\Repository\TexteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(BlocRepository $bl): Response
  {
    return $this->render('pages/home.html.twig', [
      'blocs' => $bl->findAll()
    ]);
  }

  #[Route('/add', name: 'add_bloc')]
  public function addBloc(HtmlSanitizerInterface $htmlSanitizer, Request $request, BlocRepository $br, ImageRepository $ir, TexteRepository $tr, LienRepository $lr, Validation $validation,ValidatorInterface $validator, FileUploader $fileUploader): Response
  {
    $post = $request->request;
    /** @var UserTextareaInput $textarea */
    $textarea = $post->get('txt_input');
    /** @var UploadedFile $imgFile */
    $imgFile = $request->files->get('image');

    //get token generated on form
    $submittedToken = $request->request->get('token');

    if ($this->isCsrfTokenValid('add-bloc', $submittedToken) && $post->has('submit') && ($imgFile || $textarea)) {

      $bloc = new Bloc();
      $date = new DateTime();
      $bloc->setDateCreation($date);
      $bloc->setDateModification($date);
      $br->save($bloc, true);

      if($imgFile && !$textarea)
      {
        //calling validation service to check if user upload is an image 
        $imgViolation = $validation->validateImage($imgFile,$validator);
        if(!$imgViolation)
        {
          $fileName = $fileUploader->upload($imgFile); 
          $fullImgPath = $fileUploader->getTargetDirectory().'/'.$fileName;
          dd(getimagesize($fullImgPath));
          [$width,$height] = getimagesize($fileName);


          $img = new Image();
          $img->setNomFichier($fileName);
          $img->setTypeFichier($imgFile->getClientOriginalExtension());
          $img->setBloc($bloc);
          $ir->save($img, true);
          return $this->redirectToRoute('app_home');
        }
      else{
        return new Response(
          '<h1>not an Image</h1>'
        );
      }
    }

    if($textarea){
      //Sanitize user input
      //Code will not contain any scripts, styles or other elements that can cause the website to behave or look different.
      $safeTextArea = $htmlSanitizer->sanitize($textarea);

      //calling validation service to check if user input is a url
      $urlViolation = $validation->validateUrl($safeTextArea,$validator);

      if($urlViolation){
        $txt = new Texte();
        $txt->setTexte($textarea);
        $txt->setBloc($bloc);
        $tr->save($txt, true);
        return $this->redirectToRoute('app_home');
      }
      else {
        $link = new Lien();
        $link->setUrl($textarea);
        $link->setBloc($bloc);
        $lr->save($link, true);
        return $this->redirectToRoute('app_home');
      } 
    }
    }
    else{
        return new Response(
          'Erreur de formulaire'
        );
    }
  }

  #[Route('/add-image', name: 'add_image_bloc')]
  public function addImageBloc(HtmlSanitizerInterface $htmlSanitizer, Request $request, BlocRepository $br, TexteRepository $tr, LienRepository $lr, Validation $validation,ValidatorInterface $validator): Response
  {
    // $post = $request->request;
    // $textarea = $post->get('txt_input');

    // //Sanitize user input
    // //Code will not contain any scripts, styles or other elements that can cause the website to behave or look different.
    // $safeTextArea = $htmlSanitizer->sanitize($textarea);

    // //calling validation service to check if user input is url
    // $violation = $validation->validateImage($safeTextArea,$validator);


    return $this->redirectToRoute('app_home');
  }
}
