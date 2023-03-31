<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Entity\Capsule;

use App\Form\BlocType;

use App\Repository\BlocRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/bloc')]
class BlocController extends AbstractController
{
  #[Route('/', name: 'app_bloc_index', methods: ['GET'])]
  public function blocIndex(BlocRepository $bl): Response
  {
    return $this->render('bloc/index.html.twig', [
      'blocs' => $bl->findAll()
    ]);
  }

  #[Route('/{id}', name: 'app_bloc_show', methods: ['GET'])]
  public function show(Bloc $bloc): Response
  {
    return $this->render('bloc/show.html.twig', [
      'bloc' => $bloc,
    ]);
  }

  #[Route('/{bloc_id}/{capsule_slug}/edit', name: 'app_bloc_edit', methods: ['GET', 'POST'])]
  #[ParamConverter('bloc', options: ['mapping' => ['bloc_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  public function edit(Bloc $bloc, Capsule $capsule, Request $request, BlocRepository $blocRepository): Response
  {
    $form = $this->createForm(BlocType::class, $bloc);
    // dd($form);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $blocRepository->save($bloc, true);
      return $this->redirectToRoute('capsule_index', [
        'slug_capsule' => $capsule->getSlug(),
        'slug_user' => $capsule->getUser()->getSlug()
      ]);
    }

    return $this->render('bloc/edit.html.twig', [
      'bloc' => $bloc,
      'capsule' => $capsule,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_bloc_delete', methods: ['POST'])]
  public function delete(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $bloc->getId(), $request->request->get('_token'))) {
      $blocRepository->remove($bloc, true);
    }

    return $this->redirectToRoute(
      'capsule_index',
      [
        'slug_user' => $bloc->getCapsule()->getUser()->getSlug(),
        'slug_capsule' => $bloc->getCapsule()->getSlug()
      ]
    );
  }
}
