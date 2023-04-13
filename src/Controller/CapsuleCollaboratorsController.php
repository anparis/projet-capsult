<?php

namespace App\Controller;

use App\Entity\Capsule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CapsuleCollaboratorsController extends AbstractController
{
    #[Route('/capsule/collaborators', name: 'app_capsule_collaborators')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // $capsule = $entityManager->getRepository(Capsule::class)->find(4);
      

        return $this->render('capsule_collaborators/index.html.twig', [
            'controller_name' => 'CapsuleCollaboratorsController',
        ]);
    }
}
