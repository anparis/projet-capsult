<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnectionController extends AbstractController
{
    #[Route('/connection', name: 'app_connection')]
    public function Connection(): Response
    {
        return $this->redirectToRoute('user_capsule');
    }
}
