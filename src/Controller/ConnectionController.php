<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Capsule;
use App\Entity\Connection;
use App\Repository\ConnectionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ConnectionController extends AbstractController
{
  #[Route('{slugUser}/{idCapsule}/{idBloc}/connection', name: 'app_connection')]
  #[ParamConverter('user', options: ['mapping' => ['slugUser' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['idCapsule' => 'id']])]
  #[ParamConverter('bloc', options: ['mapping' => ['idBloc' => 'id']])]
  public function Connection(Capsule $capsule, Bloc $bloc, ConnectionRepository $connectionRepository): Response
  {
    if($bloc->getCapsule()->getId() === $capsule->getId()){
      return new Response('La connexion existe-déjà');
    }
    $connection = new Connection();

    $connection->setCapsule($capsule);
    $connection->setBloc($bloc);
    $connectionRepository->save($connection, true);

    return $this->redirectToRoute('profile_index',[
      'slug' => $capsule->getUser()->getSlug()
    ]);
  }
}
