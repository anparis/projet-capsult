<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Bloc;
use App\Entity\Image;
use App\Entity\Lien;
use App\Entity\Texte;
use App\Service\Validation;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\TexteRepository;
use App\Service\FileUploader;
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
  public function addBloc(HtmlSanitizerInterface $htmlSanitizer, Request $request, BlocRepository $br, ImageRepository $ir, TexteRepository $tr, LienRepository $lr, Validation $validation,ValidatorInterface $validator, SluggerInterface $slugger): Response
  {
    $post = $request->request;
    $textarea = $post->get('txt_input');
    $imgFile = $request->files->get('image');

    //get token generated on form
    $submittedToken = $request->request->get('token');



    if ($this->isCsrfTokenValid('add-bloc', $submittedToken) && $post->has('submit') && ($imgFile || $safeTextArea)) {
      /* $bloc = new Bloc();
      $date = new DateTime();
      $bloc->setDateCreation($date);
      $bloc->setDateModification($date);
      $br->save($bloc, true); */
      if($imgFile)
      {
        //calling validation service to check if user upload is an image 
        $imgViolation = $validation->validateImage($imgFile,$validator);
        if(!$imgViolation)
        {
          dd('isanimage');
          $uploader = new FileUploader($this->getParameter('images_directory'),$slugger); 
          $fileName = $uploader->upload($imgFile);
          $img = new Image();
          $img->setNomFichier($fileName);
          $img->setTypeFichier($imgFile->guessExtension());
          $ir->save($img, true);
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
        dd("ajout texte");
        $txt = new Texte();
        $txt->setTexte($textarea);
        $txt->setBloc($bloc);
        $tr->save($txt, true);
      }
      else {
        dd("ajout lien");
        $link = new Lien();
        $link->setUrl($textarea);
        $link->setBloc($bloc);
        $lr->save($link, true);
        return new Response(
          'ajout url'
        );
      } 
    }
    
    }

    return $this->redirectToRoute('app_home');
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
