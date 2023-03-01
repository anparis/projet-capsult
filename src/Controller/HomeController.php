<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Bloc;
use App\Entity\Texte;
use App\Repository\BlocRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function home(BlocRepository $bl, EntityManagerInterface $entity, ManagerRegistry $doctrine): Response
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
  public function addBloc(EntityManagerInterface $entity, ManagerRegistry $doctrine, Request $request): Response
  {
    $post = $request->request;
    $textarea = $post->get('txt_input');
    if ($post->has('submit') && $textarea) {
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
    }
    else{
      return new Response(
        'failed'
      );
    }
    
    return $this->redirectToRoute('app_home');
  }
}
