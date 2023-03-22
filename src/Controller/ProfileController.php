<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/{slug}', name: 'profile_index')]
    public function index(User $user = null): Response
    {
      if(!$user){
        dd('erreur404');
      }

      return $this->render('profile/index.html.twig', [
        'user' => $user,
        'capsules' => $user->getCapsules()
      ]);
    }
}
