<?php

namespace App\Controller\Admin;
use App\Entity\Consultas;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Citas;
use App\Entity\Mascotas;
use App\Entity\User;
use App\Entity\Tratamientos;
use App\Entity\Medicamentos;


class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
       return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('CLINICA VETERINARIA ADMIN');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::section('Citas');
        yield MenuItem::linkToCrud('Administración de Citas', 'fas fa-list', Citas::class);
        yield MenuItem::section('Mascotas');
        yield MenuItem::linkToCrud('Administración de Mascotas', 'fas fa-list', Mascotas::class);
        yield MenuItem::section('Usuarios');
        yield MenuItem::linkToCrud('Administración de Usuarios', 'fas fa-list', User::class);
        yield MenuItem::section('Consultas');
        yield MenuItem::linkToCrud('Administración de Consultas', 'fas fa-list', Consultas::class);
        yield MenuItem::section('Tratamientos');
        yield MenuItem::linkToCrud('Administración de Tratamientos', 'fas fa-list', Tratamientos::class);
        yield MenuItem::section('Medicamentos');
        yield MenuItem::linkToCrud('Administración de Medicamentos', 'fas fa-list', Medicamentos::class);
        
    }

}



