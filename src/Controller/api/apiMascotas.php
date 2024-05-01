<?php

namespace App\Controller\api;

use App\Repository\MascotasRepository;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

    private function createResponse($mascotas): JsonResponse
    {
        $mascotasArray = [];
        foreach ($mascotas as $mascota) {
            $mascotasArray[] = [
                'id' => $mascota->getId(),
                'nombre' => $mascota->getNombre(),
                'especie' => $mascota->getEspecie(),
                'raza' => $mascota->getRaza(),
                'fechaNacimiento' => $mascota->getFechaNacimiento()->format('Y-m-d'), 
                'foto' => $mascota->getFoto(),
            ];
        }

        return new JsonResponse($mascotasArray);
    }

    #[Route('/api/mascotas/create/{idUser}', name: 'api_mascotas_create', methods: ['POST'])]
    public function createMascota(Request $request, MascotasRepository $mascotaRepository, $idUser, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $nombre = $request->get('nombre');
        $especie = $request->get('especie');
        $raza = $request->get('raza');
        $fechaNacimiento = $request->get('fechaNacimiento');


        // Obtener la foto de la solicitud HTTP
        $fotoFile = $request->files->get('foto');

        // Procesar y guardar la foto
        if ($fotoFile) {
            // Generar un nombre único y aleatorio para la imagen
            $fotoFileName = md5(uniqid()) . '.' . $fotoFile->guessExtension();

            // Mover la imagen al directorio de destino
            try {
                $fotoFile->move($this->getParameter('imagenes_directory'), $fotoFileName);
            } catch (FileException $e) {
                throw new BadRequestHttpException('No se pudo mover la imagen');
            }
            // Incluir el directorio de las imágenes en el nombre del archivo
            $fotoFileName = 'imagenes/mascotas/' . $fotoFileName;
        } else {
            // Si no se proporciona una imagen, establecer el nombre de la foto como null en la base de datos
            $fotoFileName = null;
        }

        // Obtener el objeto User correspondiente al ID del usuario
        $user = $userRepository->find($idUser);

        if (!$user) {
            return new JsonResponse(['status' => 'Error', 'message' => 'Usuario no encontrado'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Crear una nueva instancia de Mascotas y establecer los datos
        $mascota = new Mascotas();
        $mascota->setNombre($nombre);
        $mascota->setEspecie($especie);
        $mascota->setRaza($raza);
        $mascota->setFechaNacimiento(new \DateTime($fechaNacimiento));
        $mascota->setFoto($fotoFileName);
        $mascota->setUser($user);  


        // Persistir la entidad
        $entityManagerInterface->persist($mascota);
        $entityManagerInterface->flush();
        // Return a JSON response with the error message
        return new JsonResponse(['status' => 'Error', 'message' => 'Error al crear la mascota: '], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/api/mascotas/delete/{id}', name: 'api_mascotas_delete', methods: ['DELETE'])]
    public function deleteMascota(Request $request, MascotasRepository $mascotaRepository, $id, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $mascota = $mascotaRepository->find($id);

        if (!$mascota) {
            return new JsonResponse(['status' => 'Error', 'message' => 'Mascota no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManagerInterface->remove($mascota);
        $entityManagerInterface->flush();

        return new JsonResponse(['status' => 'OK', 'message' => 'Mascota eliminada'], JsonResponse::HTTP_OK);
    }
}
