<?php

namespace App\Controller\api;

use App\Repository\MascotasRepository;
use App\Service\MailerService;
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
use App\Repository\CitasRepository;

class apiMascotas extends AbstractController
{
    #[Route('/api/mascotas', name: 'api_mascotas')]
    public function getMascotas(Request $request, MascotasRepository $mascotaRepository): JsonResponse
    {
        $mascotas = $mascotaRepository->findAll();

        if (!$mascotas) {
            throw new NotFoundHttpException('Mascotas no encontradas');
        }
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

        return new JsonResponse($mascotasArray, JsonResponse::HTTP_OK);
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

        // Si no hay mascotas, devolver un array vacío
        if (!$mascotas) {
            $mascotas = [];
        }

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

        return new JsonResponse($mascotasArray, JsonResponse::HTTP_OK);
    }


    #[Route('/api/mascotas/create/{idUser}', name: 'api_mascotas_create', methods: ['POST'])]
    public function createMascota(Request $request, MascotasRepository $mascotaRepository, $idUser, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface, MailerService $mailerService): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }
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

        // Enviar un correo electrónico al usuario para notificarle que se ha registrado una nueva mascota
        $userEmail = $user->getEmail();
        $petName = $mascota->getNombre();
        $mailerService->sendPetRegisteredEmail($userEmail, $petName);

        // Return a JSON response with the success message and the created pet
        return new JsonResponse(['status' => 'Success', 'message' => 'Mascota creada correctamente', 'mascota' => $mascota], JsonResponse::HTTP_OK);
    }

    #[Route('/api/mascotas/delete/{id}', name: 'api_mascotas_delete', methods: ['DELETE'])]
    public function deleteMascota(Request $request, MascotasRepository $mascotaRepository, $id, EntityManagerInterface $entityManagerInterface, MailerService $mailerService, CitasRepository $citasRepository): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }

        $mascota = $mascotaRepository->find($id);

        if (!$mascota) {
            return new JsonResponse(['status' => 'Error', 'message' => 'Mascota no encontrada'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Obtener todas las citas asociadas a la mascota
        $citas = $citasRepository->findBy(['mascotas' => $mascota]);

        // Eliminar cada una de las citas asociadas a la mascota
        foreach ($citas as $cita) {
            $entityManagerInterface->remove($cita);
        }

        // Flushear los cambios para asegurarse de que las citas se eliminen correctamente
        $entityManagerInterface->flush();

        // Ahora podemos eliminar la mascota
        $entityManagerInterface->remove($mascota);
        $entityManagerInterface->flush();

        // Enviar un correo electrónico al usuario para notificarle que se ha eliminado una mascota
        $userEmail = $mascota->getUser()->getEmail();
        $petName = $mascota->getNombre();
        $mailerService->sendPetDeletedEmail($userEmail, $petName);

        return new JsonResponse(['status' => 'OK', 'message' => 'Mascota eliminada junto con todas sus citas'], JsonResponse::HTTP_OK);
    }
}
