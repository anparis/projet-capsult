<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Bloc;
use App\Entity\Texte;
use App\Repository\BlocRepository;
use App\Service\Validation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(BlocRepository $bl): Response
  {
    // $bloc = new Bloc();
    // $date = new DateTime('now',new DateTimeZone('Europe/Paris'));
    // $bloc->setTitre('Internet, gentil géant vert ou méchant monstre pollueur ?');
    // $bloc->setDateCreation($date);
    // $bloc->setDateModification($date);
    // $img = $doctrine->getRepository(Texte::class)->find(1);
    // $bloc->setTexte($img);

    // $img = new Image();
    // $img->setNomFichier('images/earthwindandfire.jpeg');
    // $img->setTypeFichier('jpeg');

    // $entity->persist($bloc);
    // $entity->flush();

    $blocs = $bl->findAll();
    return $this->render('pages/home.html.twig', [
      'blocs' => $blocs
    ]);
  }

  #[Route('/add', name: 'add_bloc')]
  public function addBloc(EntityManagerInterface $entity, Request $request, Validation $validation, ValidatorInterface $validator): Response
  {
    $post = $request->request;
    $textarea = $post->get('txt_input');

    $violation = $validation->validateUrl($textarea,$validator);

    if ($post->has('submit') && $textarea && $violation) {
      dd("marche");
      $bloc = new Bloc();
      $date = new DateTime();
      $bloc->setDateCreation($date);
      $bloc->setDateModification($date);
      $entity->persist($bloc);
      $entity->flush();

      $txt = new Texte();
      $txt->setTexte($textarea);
      $txt->setBloc($bloc);
      $entity->persist($txt);
      $entity->flush();
    } elseif ($violation == 0) {
      return new Response(
        'success'
      );
    } else {
      return new Response(
        'failed'
      );
    }

    return $this->redirectToRoute('app_home');
  }
}
