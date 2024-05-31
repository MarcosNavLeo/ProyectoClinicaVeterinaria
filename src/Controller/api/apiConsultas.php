<?php

//api consultas

namespace App\Controller\api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Repository\ConsultasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Citas;
use App\Entity\Tratamientos;
use App\Entity\Consultas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Mascotas;
use Symfony\Component\HttpFoundation\Response;

class apiConsultas extends AbstractController
{
    // Guardar consulta entity manager 
    #[Route('/api/consultas', name: 'api_consultas')]
    public function postConsultas(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }

        $data = json_decode($request->getContent(), true);

        $cita = $entityManager->getRepository(Citas::class)->find($data['citas_id']);
        $tratamiento = $entityManager->getRepository(Tratamientos::class)->find($data['tratamientos_id']);

        $consulta = new Consultas();
        $consulta->setCitas($cita);
        $consulta->setFechaHora(new \DateTime($data['fecha_hora']));
        $consulta->setDiagnostico($data['diagnostico']);
        $consulta->setTratamientos($tratamiento);

        $entityManager->persist($consulta);
        $entityManager->flush();

        return new JsonResponse(['id' => $consulta->getId()], JsonResponse::HTTP_CREATED);
    }

    // Obtener consultas de todas sus mascotas de un cliente
    #[Route('/api/consultas/{id}', name: 'api_consultas_get')]

    public function getConsultas($id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }
        $citas = $entityManager->getRepository(Citas::class)->findBy(['user' => $id]);

        $consultas = $entityManager->getRepository(Consultas::class)->findBy(['citas' => $citas]);

        $data = [];

        foreach ($consultas as $consulta) {
            $data[] = [
                'id' => $consulta->getId(),
                'fecha_hora' => $consulta->getFechaHora()->format('Y-m-d H:i:s'),
                'diagnostico' => $consulta->getDiagnostico(),
                'mascota' => $consulta->getCitas()->getMascotas()->getNombre(),
                'foto_mascota' => $consulta->getCitas()->getMascotas()->getFoto(),
                'tratamientos_nombre' => $consulta->getTratamientos()->getDescTratamiento(),
                'tratamientos_duracion' => $consulta->getTratamientos()->getDuracion(),
                'tratamientos_costo' => $consulta->getTratamientos()->getCosto(),
                'medicamento_nombre' => $consulta->getTratamientos()->getMedicamentos()->getNombre(),
                'medicamento_instrucciones' => $consulta->getTratamientos()->getMedicamentos()->getInstrucciones(),
                'medicamento_dosis' => $consulta->getTratamientos()->getMedicamentos()->getDosis(),
            ];
        }

        return new JsonResponse($data);

    }

    #[Route('/api/mascotas', name: 'api_mascotas_get_all')]
    public function getAllPets(EntityManagerInterface $entityManagerInterface): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new UnauthorizedHttpException('', 'Necesitas estar autenticado');
        }
        // Obtener todas las mascotas de la base de datos
        $mascotas = $entityManagerInterface->getRepository(Mascotas::class)->findAll();
        $data = [];

        // Para cada mascota...
        foreach ($mascotas as $mascota) {
            // Obtener todas las citas de la mascota
            $citas = $mascota->getCitas();

            // Añadir los datos de la mascota y sus consultas al array de datos
            $mascotaData = [
                'mascota' => $mascota->getNombre(),
                'foto_mascota' => $mascota->getFoto(),
                'consultas' => []
            ];

            // Para cada cita, obtener las consultas
            foreach ($citas as $cita) {
                foreach ($cita->getConsultas() as $consulta) {
                    $mascotaData['consultas'][] = [
                        'fecha_hora' => $consulta->getFechaHora()->format('Y-m-d H:i:s'),
                        'diagnostico' => $consulta->getDiagnostico(),
                        'tratamientos_nombre' => $consulta->getTratamientos()->getDescTratamiento(),
                        'tratamientos_duracion' => $consulta->getTratamientos()->getDuracion(),
                        'tratamientos_costo' => $consulta->getTratamientos()->getCosto(),
                        'medicamento_nombre' => $consulta->getTratamientos()->getMedicamentos()->getNombre(),
                        'medicamento_instrucciones' => $consulta->getTratamientos()->getMedicamentos()->getInstrucciones(),
                        'medicamento_dosis' => $consulta->getTratamientos()->getMedicamentos()->getDosis()
                    ];
                }
            }

            $data[] = $mascotaData;
        }

        // Devolver los datos como una respuesta JSON
        return new JsonResponse($data);
    }




}