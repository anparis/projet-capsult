<?php

namespace App\Controller;

use App\Entity\Capsule;
use App\Form\CapsuleType;
use App\Repository\CapsuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/capsule')]
class CapsuleController extends AbstractController
{
    #[Route('/', name: 'app_capsule_index', methods: ['GET'])]
    public function index(CapsuleRepository $capsuleRepository): Response
    {
        return $this->render('capsule/index.html.twig', [
            'capsules' => $capsuleRepository->findAll(),
        ]);
    }

    #[Route('/add', name: 'app_capsule_add', methods: ['GET', 'POST'])]
    public function new(Request $request, CapsuleRepository $CapsuleRepository): Response
    {
        $capsule = new Capsule();
        $form = $this->createForm(CapsuleType::class, $capsule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $CapsuleRepository->save($capsule, true);

            return $this->redirectToRoute('app_Capsule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('capsule/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_capsule_show', methods: ['GET'])]
    public function show(Capsule $capsule): Response
    {
        return $this->render('capsule/show.html.twig', [
            'capsule' => $capsule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_capsule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
    {
        $form = $this->createForm(CapsuleType::class, $capsule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $capsuleRepository->save($capsule, true);

            return $this->redirectToRoute('app_capsule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('capsule/edit.html.twig', [
            'capsule' => $capsule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_capsule_delete', methods: ['POST'])]
    public function delete(Request $request, Capsule $capsule, CapsuleRepository $capsuleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$capsule->getId(), $request->request->get('_token'))) {
            $capsuleRepository->remove($capsule, true);
        }

        return $this->redirectToRoute('app_capsule_index', [], Response::HTTP_SEE_OTHER);
    }
}
