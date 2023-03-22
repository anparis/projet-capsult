<?php

namespace App\Controller;

use App\Entity\Bloc;

use App\Form\BlocType;

use App\Repository\BlocRepository;

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

  #[Route('/{id}/edit', name: 'app_bloc_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    $form = $this->createForm(BlocType::class, $bloc);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $blocRepository->save($bloc, true);

      return $this->redirectToRoute('app_bloc_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('bloc/edit.html.twig', [
      'bloc' => $bloc,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_bloc_delete', methods: ['POST'])]
  public function delete(Request $request, Bloc $bloc, BlocRepository $blocRepository): Response
  {
    if ($this->isCsrfTokenValid('delete' . $bloc->getId(), $request->request->get('_token'))) {
      $blocRepository->remove($bloc, true);
    }

    return $this->redirectToRoute('app_bloc_index', [], Response::HTTP_SEE_OTHER);
  }
}
