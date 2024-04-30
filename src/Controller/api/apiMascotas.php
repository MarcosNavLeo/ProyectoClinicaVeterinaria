<?php

namespace App\Controller\api;

use App\Repository\MascotasRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\Mascotas;
use Doctrine\ORM\EntityManagerInterface;


class apiMascotas extends AbstractController
{
    #[Route('/api/mascotas', name: 'api_mascotas')]
    public function getMascotas(Request $request, MascotasRepository $mascotaRepository): JsonResponse
    {
        $mascotas = $mascotaRepository->findAll();

        if (!$mascotas) {
            throw new NotFoundHttpException('Mascotas no encontradas');
        }

        return $this->createResponse($mascotas);
    }

    #[Route('/api/mascotas/{id}', name: 'api_mascotas_user')]
    public function getMascotasUser(Request $request, UserRepository $userRepository, MascotasRepository $mascotaRepository, $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Usuario no encontrado');
        }

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }

        $mascotas = $mascotaRepository->findBy(['user' => $id]);

        if (!$mascotas) {
            throw new NotFoundHttpException('Mascotas no encontradas');
        }

        return $this->createResponse($mascotas);
    }

    #[Route('/api/mascotas/{nombre}/{parametro}', name: 'api_mascota_categoria')]
    public function getMascotasByCategoria(Request $request, MascotasRepository $mascotaRepository, $nombre, $parametro): JsonResponse
    {
        $mascotas = $mascotaRepository->findByLocalidadOProvincia($parametro, $nombre);

        if (!$mascotas) {
            throw new NotFoundHttpException('Mascotas no encontradas');
        }

        return $this->createResponse($mascotas);
    }

    private function createResponse($mascotas): JsonResponse
    {
        $mascotasArray = [];
        foreach ($mascotas as $mascota) {
            $mascotasArray[] = [
                'id' => $mascota->getId(),
                'nombre' => $mascota->getNombre(),
                'especie' => $mascota->getEspecie(),
                'raza' => $mascota->getRaza(),
                'foto' => $mascota->getFoto(),
            ];
        }

        return new JsonResponse($mascotasArray);
    }
    #[Route('/api/mascotas/{idUser}', name: 'api_mascotas_create', methods: ['POST'])]
    public function createMascota(Request $request, MascotasRepository $mascotaRepository, EntityManagerInterface $entityManager,$idUser): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }

        if (!isset($data['nombre']) || !isset($data['especie']) || !isset($data['raza']) || !isset($data['foto']) || !isset($data['fechaNacimiento']) || !isset($data['user'])) {
            return new JsonResponse(['status' => 'Error', 'message' => 'Faltan datos necesarios'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $nombre = $data['nombre'];
        $especie = $data['especie'];
        $raza = $data['raza'];
        $foto = $data['foto'];
        $fechaNacimiento = $data['fechaNacimiento'];

        $mascota = new Mascotas();
        $mascota->setNombre($nombre);
        $mascota->setEspecie($especie);
        $mascota->setRaza($raza);
        $mascota->setFoto($foto);
        $mascota->setFechaNacimiento($fechaNacimiento);
        $mascota->setUser($idUser);

        $entityManager->persist($mascota);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Mascota creada'], JsonResponse::HTTP_CREATED);
    }
}


