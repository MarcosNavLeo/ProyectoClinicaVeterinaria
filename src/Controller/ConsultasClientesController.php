<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsultasClientesController extends AbstractController
{
    #[Route('/consultas/clientes', name: 'app_consultas_clientes')]
    public function index(): Response
    {
        return $this->render('consultas_clientes/index.html.twig', [
            'controller_name' => 'ConsultasClientesController',
        ]);
    }
}
