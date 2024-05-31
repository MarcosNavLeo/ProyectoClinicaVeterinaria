<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistorialMascotasController extends AbstractController
{
    #[Route('/historial/mascotas', name: 'app_historial_mascotas')]
    public function index(): Response
    {
        return $this->render('historial_mascotas/index.html.twig', [
            'controller_name' => 'HistorialMascotasController',
        ]);
    }
}
