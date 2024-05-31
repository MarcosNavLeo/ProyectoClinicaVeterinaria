<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RedirectController extends AbstractController
{
    #[Route('/redirect', name: 'redirect')]
    public function index(Security $security)
    {
        $user = $security->getUser();

        if ($user && $security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }
        if ($user && $security->isGranted('ROLE_VETERINARIO')) {
            return $this->redirectToRoute('app_citas_veterinario');
        }

        return $this->redirectToRoute('app_home');
    }
}