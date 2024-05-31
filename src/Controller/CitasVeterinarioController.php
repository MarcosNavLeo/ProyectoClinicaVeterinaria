<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CitasVeterinarioController extends AbstractController
{
    #[Route('/citas_veterinario', name: 'app_citas_veterinario')]
    public function index(): Response
    {
        return $this->render('citas_veterinario/index.html.twig', [
            'controller_name' => 'CitasVeterinarioController',
        ]);
    }
}
