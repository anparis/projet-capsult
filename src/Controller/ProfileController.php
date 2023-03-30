<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Capsule;
use App\Form\CapsuleType;
use App\Repository\CapsuleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
  // #[Route('/{slug}', name: 'profile_index')]
  public function index(User $user = null): Response
  {
    return $this->render('profile/index.html.twig', [
      'user' => $user,
      'capsules' => $user->getCapsules()
    ]);
  }

  // #[Route('/add_capsule/{id}', name: 'profile_add_capsule', methods: ['POST'])]
  public function addCapsule(User $user, SluggerInterface $sluger, Request $request, CapsuleRepository $capsuleRepository): Response
  {
    $capsule = new Capsule();
    $form = $this->createForm(CapsuleType::class, $capsule);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // $capsule->setSlug($sluger->slug($form->get('title')->getData())->lower());
      ($form->get('title')->getData() == 'open') ? $capsule->setOpen(1) : $capsule->setOpen(0);
      $capsule->setUser($user);

      $capsuleRepository->save($capsule, true);

      return $this->redirectToRoute('profile_index', [
        'slug' => $user->getSlug()
      ]);
    }

    return $this->render('capsule/add.html.twig', [
      'form' => $form,
      'user' => $user
    ]);
  }
}
