<?php

namespace App\Controller;

use DateTime;
use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\Image;
use App\Entity\Texte;
use App\Service\Validation;
use App\Service\FileUploader;
use Knp\Snappy\Image as knpImage;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\ImageRepository;
use App\Repository\TexteRepository;
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
    return $this->render('pages/home.html.twig', [
      'blocs' => $bl->findAll()
    ]);
  }

  #[Route('/add', name: 'add_bloc')]
  public function addBloc(HtmlSanitizerInterface $htmlSanitizer, Request $request, ImageRepository $ir, TexteRepository $tr, LienRepository $lr, Validation $validation, ValidatorInterface $validator, FileUploader $fileUploader,knpImage $knpSnappyImage): Response
  {
    $post = $request->request;
    /** $textarea */
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

      if ($imgFile && !$textarea) {
        //calling validation service to check if user upload is an image 
        $imgViolation = $validation->validateImage($imgFile, $validator);
        if ($imgViolation) {
          return new Response(
            '<h1>not an Image</h1>'
          );
        } else {
          $img = new Image();
          $fileName = $fileUploader->upload($imgFile);
          $img->setNomFichier($fileName);
          $img->setTypeFichier($imgFile->getClientOriginalExtension());
          $img->setBloc($bloc);

          // $imageName = $img->getNomFichier();
          // $fullSizeImgPath = $fileUploader->getTargetDirectory() . '/' . $imageName;
          // $imageOptimizer->resize($fullSizeImgPath);

          //using ImageRepository save method to persist and flush
          $ir->save($img, true);
          return $this->redirectToRoute('app_home');
        }
      }
      if ($textarea && !$imgFile) {
        //Sanitize user input
        //Code will not contain any scripts, styles or other elements that can cause the website to behave or look different.
        $safeTextArea = $htmlSanitizer->sanitize($textarea);

        //calling validation service to check if user input is a url
        $urlViolation = $validation->validateUrl($safeTextArea, $validator);

        if ($urlViolation) {
          $txt = new Texte();
          $txt->setTexte($safeTextArea);
          $txt->setBloc($bloc);
          $tr->save($txt, true);
          return $this->redirectToRoute('app_home');
        } else {
          $link = new Lien();
          $link->setUrl($safeTextArea);
          $link->setBloc($bloc);
          $lr->save($link, true);

          // $knpSnappyImage->generate($safeTextArea, 'uploads/bloc_img/test.png');
          
          return $this->redirectToRoute('app_home');
        }
      }
      // user can't submit textarea and upload file at the same time
      return $this->redirectToRoute('app_home');
    } else {
      return new Response(
        'Erreur de formulaire'
      );
    }
  }
}
