<?php

namespace App\Controller;

use App\Entity\Bloc;
use App\Form\BlocType;

use App\Entity\Capsule;

use App\Repository\BlocRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

  /**
   * This controller allow us to edit a bloc
   * @param Bloc $bloc
   * @param Request $resquest
   * @param Capsule $capsule
   * @param BlocRepository $blocRepository
   * @return Response 
  **/
  #[Route('/{bloc_id}/{capsule_slug}/edit', name: 'app_bloc_edit', methods: ['GET', 'POST'])]
  #[ParamConverter('bloc', options: ['mapping' => ['bloc_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  #[Security("is_granted('ROLE_USER') and (user === bloc.getCapsule().getUser() or capsule.isUserCollaborator(user))")]
  public function editBloc(Bloc $bloc, Capsule $capsule, Request $request, BlocRepository $blocRepository): Response
  {
    $form = $this->createForm(BlocType::class, $bloc);
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

  /**
   * This controller allow us to edit a bloc
   * @param Bloc $bloc
   * @param Request $resquest
   * @param Capsule $capsule
   * @param BlocRepository $blocRepository
   * @return Response 
  **/
  #[Route('/{bloc_id}/{capsule_slug}/show', name: 'app_bloc_show', methods: ['GET'])]
  #[ParamConverter('bloc', options: ['mapping' => ['bloc_id' => 'id']])]
  #[ParamConverter('capsule', options: ['mapping' => ['capsule_slug' => 'slug']])]
  public function showBloc(Bloc $bloc, Capsule $capsule): Response
  {
    return $this->render('bloc/show.html.twig', [
      'bloc' => $bloc,
      'capsule' => $capsule
    ]);
  }

  #[Route('/{id}', name: 'app_bloc_delete', methods: ['POST'])]
  #[Security("is_granted('ROLE_USER') and user === bloc.getCapsule().getUser()")]
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
