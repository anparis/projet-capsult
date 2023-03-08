<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Bloc;
use App\Entity\Lien;
use App\Entity\Texte;
use App\Service\Validation;
use App\Repository\BlocRepository;
use App\Repository\LienRepository;
use App\Repository\TexteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
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
  public function addBloc(HtmlSanitizerInterface $htmlSanitizer, Request $request, BlocRepository $br, TexteRepository $tr, LienRepository $lr, Validation $validation,ValidatorInterface $validator): Response
  {
    $post = $request->request;
    $textarea = $post->get('txt_input');
    //get token generated on form
    $submittedToken = $request->request->get('token');
    $img = $request->files->get('image');
    $validation->validateImage($img,$validator);
    dd($validation->validateImage($img,$validator));

    //Sanitize user input
    //Code will not contain any scripts, styles or other elements that can cause the website to behave or look different.
    $safeTextArea = $htmlSanitizer->sanitize($textarea);

    //calling validation service to check if user input is a url
    $urlViolation = $validation->validateUrl($safeTextArea,$validator);

    if ($this->isCsrfTokenValid('add-bloc', $submittedToken) && $post->has('submit') && ($safeTextArea || $file)) {
      // $bloc = new Bloc();
      // $date = new DateTime();
      // $bloc->setDateCreation($date);
      // $bloc->setDateModification($date);
      // $br->save($bloc, true);
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
    } else {
      return new Response(
        'error'
      );
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
