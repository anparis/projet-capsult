<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use Doctrine\ORM\EntityManager;
use App\Repository\CapsuleRepository;
use App\Repository\ConnectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProfileController extends AbstractController
{
  #[Route('/{slug}', name: 'profile_index')]
  public function index(User $user = null, EntityManagerInterface $entityManager): Response
  {
    if (!$user) {
      return $this->redirectToRoute('app_home');
    }
    $capsules = $entityManager->getRepository(Capsule::class)->findBy(['user' => $user->getId()], ['updated_at' => 'DESC']);

    if (!empty($capsules)) {
      $allCapsules = array_merge($capsules, $user->getCapsulesCollabs()->toArray());

      usort($allCapsules, function ($a, $b) {
        return $a->getUpdatedAt() < $b->getUpdatedAt();
      });
    }

    return $this->render('profile/index.html.twig', [
      'user' => $user,
      'capsules' => $capsules ? $allCapsules : null
    ]);
  }

  #[Route('/add_capsule/{id}', name: 'profile_add_capsule', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === current_user")]
  public function addCapsule(User $current_user, Request $request, CapsuleRepository $capsuleRepository): Response
  {
    $capsule = new Capsule();
    $form = $this->createForm(CapsuleType::class, $capsule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      ($form->get('status')->getData() == 'open') ? $capsule->setOpen(1) : $capsule->setOpen(0);

      $capsule->setUser($current_user);

      $capsuleRepository->save($capsule, true);

      return $this->redirectToRoute('profile_index', [
        'slug' => $current_user->getSlug()
      ]);
    }

    return $this->render('capsule/add.html.twig', [
      'form' => $form,
      'user' => $current_user,
      'capsule' => null
    ]);
  }

  #[Route('/{id}/delete-user', name: 'app_user_delete', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === user")]
  public function deleteUser(Request $request, User $user, UserRepository $userRepository, ConnectionRepository $connectionRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
      
      foreach ($user->getBlocs() as $bloc) {
        $user->removeBloc($bloc);
      }

      $userRepository->remove($user, true);

      $request->getSession()->invalidate();
      $this->container->get('security.token_storage')->setToken(null);

      return $this->redirectToRoute('app_home');
    }
  
    return $this->redirectToRoute('profile_index', [
      'slug' => $this->getUser()->getSlug(),
    ]);
  }

}

