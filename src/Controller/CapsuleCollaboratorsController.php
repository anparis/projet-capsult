<?php

namespace App\Controller;

use App\Entity\Capsule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

    #[Route('/collaborator/capsule/{id}', name: 'capsule_collaborator')]
    #[Security("is_granted('ROLE_USER')")]
    public function capsuleCollaborator(Capsule $capsule, EntityManagerInterface $entityManager): Response
    {
      $user = $this->getUser();

      if($capsule->isUserCollaborator($user))
      {
        $capsule->removeCollaborator($user);
        $entityManager->flush();

        return $this->json([
          'message' => 'Vous avez arrêté la collaboration',
        ]);
      }
      
      $capsule->addCollaborator($user);
      $entityManager->flush();

      return $this->json([
        'message' => 'Une nouvelle collaboration a été créée',
      ]);
    }
}
