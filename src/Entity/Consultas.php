<?php

namespace App\Entity;

use App\Repository\ConsultasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: ConsultasRepository::class)]
#[Broadcast]
class Consultas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha_hora = null;

    #[ORM\Column(length: 150)]
    private ?string $sintomas = null;

    #[ORM\Column(length: 255)]
    private ?string $diagnostico = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?citas $citas = null;

    #[ORM\ManyToOne(inversedBy: 'consultas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tratamientos $tratamientos = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaHora(): ?\DateTimeInterface
    {
        return $this->fecha_hora;
    }

    public function setFechaHora(\DateTimeInterface $fecha_hora): static
    {
        $this->fecha_hora = $fecha_hora;

        return $this;
    }

    public function getSintomas(): ?string
    {
        return $this->sintomas;
    }

    public function setSintomas(string $sintomas): static
    {
        $this->sintomas = $sintomas;

        return $this;
    }

    public function getDiagnostico(): ?string
    {
        return $this->diagnostico;
    }

    public function setDiagnostico(string $diagnostico): static
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    public function getCitas(): ?citas
    {
        return $this->citas;
    }

    public function setCitas(?citas $citas): static
    {
        $this->citas = $citas;

        return $this;
    }

    public function getTratamientos(): ?tratamientos
    {
        return $this->tratamientos;
    }

    public function setTratamientos(?tratamientos $tratamientos): static
    {
        $this->tratamientos = $tratamientos;

        return $this;
    }
}
