<?php

namespace App\Controller;

use App\Entity\Capsule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CapsuleLikeController extends AbstractController
{
    #[Route('/like/capsule/{id}', name: 'capsule_like')]
    #[Security("is_granted('ROLE_USER')")]
    public function capsuleLike(Capsule $capsule, EntityManagerInterface $entityManager): Response
    {
      $user = $this->getUser();

      if($capsule->isLikedByUser($user))
      {
        $capsule->removelike($user);
        $entityManager->flush();

        return $this->json([
          'message' => 'Le like a été supprimé',
        ]);
      }
      
      $capsule->addLike($user);
      $entityManager->flush();

      return $this->json([
        'message' => 'Le like a été ajouté',
      ]);
    }
}
