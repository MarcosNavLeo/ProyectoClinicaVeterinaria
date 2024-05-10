<?php

namespace App\Controller\api;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\CitasRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use DateTime;
use App\Entity\Citas;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MascotasRepository;


class apiCitas extends AbstractController
{
    //obtener citas de un user
    #[Route('/api/citas/{id}', name: 'api_citas_user')]
    public function getCitas($id, Request $request, CitasRepository $citaRepository, UserRepository $user): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }
        $user = $user->find($id);
        if (!$user) {
            throw new NotFoundHttpException('Usuario no encontrado');
        }
        $citas = $citaRepository->findBy(['user' => $user]);

        $citasArray = [];
        foreach ($citas as $cita) {
            // Ajustar la hora de finalización sumando 1 hora a la hora de inicio
            $end_time = clone $cita->getHora();
            if (!($end_time instanceof DateTime)) {
                $end_time = new DateTime($end_time->format('Y-m-d H:i:s'));
            }
            $end_time->modify('+1 hour');

            $citasArray[] = [
                'id' => $cita->getId(),
                'title' => $cita->getMascotas()->getNombre(), // título del evento
                'start' => $cita->getFecha()->format('Y-m-d') . 'T' . $cita->getHora()->format('H:i'), // fecha y hora de inicio del evento
                'end' => $cita->getFecha()->format('Y-m-d') . 'T' . $end_time->format('H:i'), // fecha y hora de finalización del evento
                'color' => '#808080', // color del evento,
                'motivo' => $cita->getMotivo(), // motivo de la cita
            ];
        }

        return new JsonResponse($citasArray, JsonResponse::HTTP_OK);
    }
    //obtener todas las citas
    #[Route('/api/citas', name: 'api_citas')]
    public function getCitasAll(Request $request, CitasRepository $citaRepository): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }

        $citas = $citaRepository->findAll();

        $citasArray = [];
        foreach ($citas as $cita) {
            // Ajustar la hora de finalización sumando 1 hora a la hora de inicio
            $end_time = clone $cita->getHora();
            if (!($end_time instanceof DateTime)) {
                $end_time = new DateTime($end_time->format('Y-m-d H:i:s'));
            }
            $end_time->modify('+1 hour');

            $citasArray[] = [
                'id' => $cita->getId(),
                'title' => $cita->getMascotas()->getNombre(), // título del evento
                'start' => $cita->getFecha()->format('Y-m-d') . 'T' . $cita->getHora()->format('H:i'), // fecha y hora de inicio del evento
                'end' => $cita->getFecha()->format('Y-m-d') . 'T' . $end_time->format('H:i'), // fecha y hora de finalización del evento
                'color' => '#808080', // color del evento,
                'motivo' => $cita->getMotivo(), // motivo de la cita
            ];
        }

        return new JsonResponse($citasArray, JsonResponse::HTTP_OK);
    }

    //crear cita
    #[Route('/api/crear/citas', name: 'api_citas_create', methods: ['POST'])]
    public function createCita(Request $request, CitasRepository $citaRepository, UserRepository $userRepository, MascotasRepository $mascotasRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }

        // Cambiamos la forma de obtener los datos
        $data = $request->request->all();

        // Imprime el valor de idUser en el log
        error_log('idUser: ' . $data['idUser']);

        $user = $userRepository->find($data['idUser']);

        if (!$user) {
            throw new NotFoundHttpException('Usuario no encontrado');
        }

        $mascota = $mascotasRepository->find($data['mascota']);
        if (!$mascota) {
            throw new NotFoundHttpException('Mascota no encontrada');
        }

        $cita = new Citas();
        $cita->setFecha(new DateTime($data['fecha']));
        $cita->setHora(new DateTime($data['hora']));
        $cita->setMotivo($data['motivo']);
        $cita->setMascotas($mascota);
        $cita->setUser($user);

        $entityManager->persist($cita);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Cita creada'], JsonResponse::HTTP_CREATED);
    }

    //borrar cita
    #[Route('/api/citas/delete/{id}', name: 'api_citas_delete', methods: ['DELETE'])]
    public function deleteCita(Request $request, CitasRepository $citaRepository, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('Necesitas estar autenticado');
        }

        $cita = $citaRepository->find($id);
        if (!$cita) {
            throw new NotFoundHttpException('Cita no encontrada');
        }

        $entityManager->remove($cita);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Cita eliminada'], JsonResponse::HTTP_OK);
    }



}