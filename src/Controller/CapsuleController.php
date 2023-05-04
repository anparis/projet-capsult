<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\User;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use App\Repository\CapsuleRepository;
use App\Repository\ConnectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CapsuleController extends AbstractController
{
  
  #[Route('/{slug_user}/{slug_capsule}', name: 'capsule_index', methods: ['GET'])]
  #[ParamConverter('user', options: ['mapping' => ['slug_user' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['slug_capsule' => 'slug']])]
  public function index(User $user=null, Capsule $capsule=null, CapsuleRepository $capsuleRepository): Response
  {
    // Check wether capsule user match user or user match a collaborator in capsule collaborator
    if (!$user || !$capsule || ($capsule->getUser() !== $user and !$capsule->getCollaborators()->contains($user))) {
      throw new NotFoundHttpException('Capsule non trouvée');
    }

    // Check whether or not capsule possessed blocs
    $connectedBlocCol = $capsule->getConnections()->isEmpty();

    if ($connectedBlocCol) {
      return $this->render('capsule/index.html.twig', [
        'capsule' => $capsule,
        'user' => $user,
        'capsules' => $user->getCapsules(),
        'blocs' => null,
        'explorable_capsules' => $capsuleRepository->findExplorableCapsules()
      ]);
    } else {
      //I associate a key with a connected bloc and his connection date or his date of last update
      //If creator of bloc same as creator of capsule, date will be the last update date
      //Else, date will be the date of connection in capsule
      foreach ($capsule->getConnections() as $connection) {
        //check to verify if bloc has not an anonymous user
        if ($connection->getBloc()->getUser()) {
          if ($connection->getBloc()->getUser() === $connection->getCapsule()->getUser()) {
            $blocsConnected[] = ['bloc' => $connection->getBloc(), 'date' => $connection->getBloc()->getUpdatedAt()];
          } else
            $blocsConnected[] = ['bloc' => $connection->getBloc(), 'date' => $connection->getCreatedAt()];
        } else
          $blocsConnected[] = ['bloc' => $connection->getBloc(), 'date' => $connection->getCreatedAt()];
      }
    }

    //sorting array of object by descending date with usort
    usort($blocsConnected, function ($a, $b) {
      return $a['date'] < $b['date'];
    });

    return $this->render('capsule/index.html.twig', [
      'capsule' => $capsule,
      'user' => $user,
      'capsules' => $user->getCapsules(),
      'blocs' => $blocsConnected,
      'explorable_capsules' => $capsuleRepository->findExplorableCapsules()
    ]);
  }


  #[Route('/{user_slug}/{capsule_slug}/{id}/capsules-connection', name: 'capsules_connection', methods: ['GET'])]
  #[ParamConverter('bloc', options: ['mapping' => ['id' => 'id']])]
  #[ParamConverter('user', options: ['mapping' => ['user_slug' => 'slug']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  public function userConnections(Bloc $bloc, User $user, Capsule $capsule): Response
  {

    return $this->render('connection/index.html.twig', [
      'capsules' => $user->getCapsules(),
      'current_capsule' => $capsule,
      'bloc' => $bloc,
      'user' => $user
    ]);
  }


  #[Route('/{id}/edit', name: 'capsule_edit', methods: ['GET', 'POST'])]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function editCapsule(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
  {
    $form = $this->createForm(CapsuleType::class, $capsule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $capsuleRepository->save($capsule, true);

      return $this->redirectToRoute(
        'capsule_index',
        [
          'slug_user' => $capsule->getUser()->getSlug(),
          'slug_capsule' => $capsule->getSlug()
        ]
      );
    }

    return $this->render('capsule/edit.html.twig', [
      'capsule' => $capsule,
      'form' => $form,
    ]);
  }

  // Show capsules details
  #[Route('/{id}/show', name: 'capsule_show', methods: ['GET'])]
  public function showCapsule(Capsule $capsule): Response
  {
    return $this->render(
      'capsule/show.html.twig',
      [
        'capsule' => $capsule,
      ]
    );
  }

  #[Route('/add_capsule/{id}', name: 'add_capsule', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === current_capsule.getUser()")]
  public function addCapsule(Capsule $current_capsule, Request $request, CapsuleRepository $capsuleRepository): Response
  {
    $user = $this->getUser();
    $capsule = new Capsule();
    $form = $this->createForm(CapsuleType::class, $capsule);
    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {
      ($form->get('status')->getData() === 'open') ? $capsule->setOpen(1) : $capsule->setOpen(0);
      $capsule->setUser($user);

      $capsuleRepository->save($capsule, true);

      return $this->redirectToRoute('capsule_index', [
        'slug_user' => $user->getSlug(),
        'slug_capsule' => $current_capsule->getSlug()
      ]);
    }

    return $this->render('capsule/add.html.twig', [
      'form' => $form,
      'user' => $user,
      'capsule' => $current_capsule
    ]);
  }

  #[Route('/{slug}/{id}/delete-capsule', name: 'app_capsule_delete', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function deleteCapsule(Request $request, string $slug, Capsule $capsule, CapsuleRepository $capsuleRepository, ConnectionRepository $connectionRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $capsule->getId(), $request->request->get('_token'))) {
      foreach ($capsule->getConnections() as $connection) {
        $connectionRepository->remove($connection, true);
      }
      foreach ($capsule->getBlocs() as $bloc) {
        $capsule->removeBloc($bloc);
      }
      $capsuleRepository->remove($capsule, true);
    }

    return $this->redirectToRoute('profile_index', [
      'slug' => $slug
    ]);
  }

  // Update the status of the Capsule (sealed or open)
  #[Route('/status/capsule/{id}', name: 'capsule_status')]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function capsuleStatus(Capsule $capsule, EntityManagerInterface $entityManager): Response
  {
    if ($capsule->isOpen()) {
      $capsule->setStatus('sealed');
      $entityManager->flush();

      return $this->json([
        'message' => 'La capsule est scellée',
        'status_fr' => 'statut > scellée'
      ]);
    }

    $capsule->setStatus('open');
    $entityManager->flush();

    return $this->json([
      'message' => 'La capsule est ouverte',
      'status_fr' => 'statut > ouverte'
    ]);
  }

  // Add or remove the Capsule exploration
  #[Route('/explore/capsule/{id}', name: 'capsule_explore')]
  #[Security("is_granted('ROLE_USER') and user === capsule.getUser()")]
  public function capsuleExplore(Capsule $capsule, EntityManagerInterface $entityManager): Response
  {
    if ($capsule->getExplore()) {
      $capsule->setExplore(0);
      $entityManager->flush();

      return $this->json([
        'message' => 'La capsule est de retour sur terre U+1F30D'
      ]);
    }

    $capsule->setExplore(1);
    $entityManager->flush();

    return $this->json([
      'message' => 'La capsule est dans l\'espace U+1F680',
    ]);
  }
}
