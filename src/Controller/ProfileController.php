<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use Doctrine\ORM\EntityManager;
use App\Repository\CapsuleRepository;
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
    if ($user == null) {
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
      ($form->get('title')->getData() == 'open') ? $capsule->setOpen(1) : $capsule->setOpen(0);
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
}
