<?php

namespace App\Controller\api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Repository\TratamientosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Tratamientos;

class apiTratamientos extends AbstractController
{
    #[Route('/api/tratamientos', name: 'api_tratamientos')]
    public function getTratamientosAll(Request $request, TratamientosRepository $tratamientoRepository): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }

        $tratamientos = $tratamientoRepository->findAll();

        $tratamientosArray = [];
        foreach ($tratamientos as $tratamiento) {
            $tratamientosArray[] = [
                'id' => $tratamiento->getId(),
                'descripcion' => $tratamiento->getDescTratamiento(),
                'fecha_tratamiento' => $tratamiento->getFechaTratamiento()->format('Y-m-d'),
                'duracion' => $tratamiento->getDuracion(),
                'costo' => $tratamiento->getCosto(),
                'medicamento' => $tratamiento->getMedicamentos() ? $tratamiento->getMedicamentos()->getNombre() : null,
            ];
        }

        return new JsonResponse($tratamientosArray, JsonResponse::HTTP_OK);
    }

    //Crear tratamientos
    #[Route('/api/tratamientos', name: 'api_tratamientos_create', methods: ['POST'])]
    public function createTratamientos(Request $request,EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }

        $data = json_decode($request->getContent(), true);

        $tratamiento = new Tratamientos();
        $tratamiento->setDescTratamiento($data['descripcion']);
        $tratamiento->setFechaTratamiento(new \DateTime($data['fecha_tratamiento']));
        $tratamiento->setDuracion($data['duracion']);
        $tratamiento->setCosto($data['costo']);

        $entityManagerInterface->persist($tratamiento);
        $entityManagerInterface->flush();

        return new JsonResponse(['status' => 'Tratamiento creado'], JsonResponse::HTTP_CREATED);
    }

    //Actualizar tratamientos
    #[Route('/api/tratamientos/{id}', name: 'api_tratamientos_update', methods: ['PUT'])]
    public function updateTratamientos($id, Request $request, TratamientosRepository $tratamientoRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }

        $tratamiento = $tratamientoRepository->find($id);

        if (!$tratamiento) {
            return new JsonResponse(['status' => 'No existe el tratamiento'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $tratamiento->setDescTratamiento($data['descripcion']);
        $tratamiento->setFechaTratamiento(new \DateTime($data['fecha_tratamiento']));
        $tratamiento->setDuracion($data['duracion']);
        $tratamiento->setCosto($data['costo']);

        $entityManagerInterface->persist($tratamiento);
        $entityManagerInterface->flush();

        return new JsonResponse(['status' => 'Tratamiento actualizado'], JsonResponse::HTTP_OK);
    }

    //Eliminar tratamientos
    #[Route('/api/tratamientos/{id}', name: 'api_tratamientos_delete', methods: ['DELETE'])]
    public function deleteTratamientos($id, TratamientosRepository $tratamientoRepository, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }

        $tratamiento = $tratamientoRepository->find($id);

        if (!$tratamiento) {
            return new JsonResponse(['status' => 'No existe el tratamiento'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManagerInterface->remove($tratamiento);
        $entityManagerInterface->flush();

        return new JsonResponse(['status' => 'Tratamiento eliminado'], JsonResponse::HTTP_OK);
    }
}

