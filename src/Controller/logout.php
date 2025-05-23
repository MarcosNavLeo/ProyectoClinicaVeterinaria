<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class logout extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function index(): Response
    {
        return $this->render('logout/index.html.twig', [
            'controller_name' => 'logout',
        ]);
    }
}