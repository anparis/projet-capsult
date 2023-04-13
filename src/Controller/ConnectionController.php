<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\User;
use App\Entity\Capsule;
use App\Entity\Connection;
use App\Repository\BlocRepository;
use App\Repository\CapsuleRepository;
use App\Repository\ConnectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ConnectionController extends AbstractController
{
  #[Route('{slugUser}/{slugCapsule}/{idCapsule}/{idBloc}/connection', name: 'app_connection')]
  #[ParamConverter('current_user', options: ['mapping' => ['slugUser' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['idCapsule' => 'id']])]
  #[ParamConverter('bloc', options: ['mapping' => ['idBloc' => 'id']])]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function connection(string $slugCapsule, Capsule $capsule, User $current_user, Bloc $bloc, ConnectionRepository $connectionRepository): Response
  {
    $connection = new Connection();

    $connection->setCapsule($capsule);
    $connection->setBloc($bloc);
    $connectionRepository->save($connection, true);

    return $this->redirectToRoute('capsule_index',[
      'slug_user' => $current_user->getSlug(),
      'slug_capsule' => $slugCapsule
    ]);
  }

  #[Route('/{user_id}/{capsule_id}/{bloc_id}/delete', name: 'app_connection_delete', methods: ['POST'])]
  #[ParamConverter('user', options: ['mapping' => ['user_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_id' => 'id']])]
  #[ParamConverter('bloc', options: ['mapping' => ['bloc_id' => 'id']])]
  #[Security("is_granted('ROLE_USER') and (user === capsule.getUser() or capsule.isUserCollaborator(user))")]
  public function delete(User $user,Capsule $capsule, Request $request, Bloc $bloc, ConnectionRepository $connectionRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $bloc->getId(), $request->request->get('_token'))) {
      $connection = $connectionRepository->findOneBy(['capsule' => $capsule->getId(), 'bloc'=> $bloc->getId()]);

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
