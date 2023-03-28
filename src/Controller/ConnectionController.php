<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Capsule;
use App\Entity\Connection;
use App\Entity\User;
use App\Repository\ConnectionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ConnectionController extends AbstractController
{
  #[Route('{slugUser}/{slugCapsule}/{idCapsule}/{idBloc}/connection', name: 'app_connection')]
  #[ParamConverter('user', options: ['mapping' => ['slugUser' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['idCapsule' => 'id']])]
  #[ParamConverter('bloc', options: ['mapping' => ['idBloc' => 'id']])]
  public function Connection(string $slugCapsule, Capsule $capsule, User $user, Bloc $bloc, ConnectionRepository $connectionRepository): Response
  {
    $connection = new Connection();

    $connection->setCapsule($capsule);
    $connection->setBloc($bloc);
    $connectionRepository->save($connection, true);

    return $this->redirectToRoute('capsule_index',[
      'slug_user' => $user->getSlug(),
      'slug_capsule' => $slugCapsule
    ]);
  }
}
