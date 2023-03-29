<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\User;
use App\Entity\Capsule;
use App\Entity\Connection;
use App\Repository\BlocRepository;
use App\Repository\ConnectionRepository;
use Symfony\Component\HttpFoundation\Request;
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

  #[Route('/{user_id}/{capsule_id}/{bloc_id}', name: 'app_connection_delete', methods: ['POST'])]
  #[ParamConverter('user', options: ['mapping' => ['user_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_id' => 'id']])]
  #[ParamConverter('connection', options: ['mapping' => ['capsule_id' => 'capsule_id']])]
  #[ParamConverter('connection', options: ['mapping' => ['bloc_id' => 'bloc_id']])]
  public function delete(User $user,Capsule $capsule, Request $request, Connection $connection, ConnectionRepository $connectionRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $connection->getBloc()->getId(), $request->request->get('_token'))) {
      $connectionRepository->remove($connection, true);
    }

    return $this->redirectToRoute(
      'capsule_index',
      [
        'slug_user' => $user->getSlug(),
        'slug_capsule' => $capsule->getSlug()
      ]
    );
  }
}
