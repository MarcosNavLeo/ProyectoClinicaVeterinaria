<?php

namespace App\Entity;

use App\Repository\TratamientosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TratamientosRepository::class)]
#[Broadcast]
class Tratamientos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $desc_tratamiento = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_tratamiento = null;

    #[ORM\Column(length: 100)]
    private ?string $duracion = null;

    #[ORM\Column]
    private ?float $costo = null;

    #[ORM\OneToMany(targetEntity: Consultas::class, mappedBy: 'tratamientos')]
    private Collection $consultas;

    #[ORM\ManyToOne(inversedBy: 'tratamientos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?medicamentos $medicamentos = null;

    public function __construct()
    {
        $this->citas = new ArrayCollection();
        $this->consultas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescTratamiento(): ?string
    {
        return $this->desc_tratamiento;
    }

    public function setDescTratamiento(string $desc_tratamiento): static
    {
        $this->desc_tratamiento = $desc_tratamiento;

        return $this;
    }

    public function getFechaTratamiento(): ?\DateTimeInterface
    {
        return $this->fecha_tratamiento;
    }

    public function setFechaTratamiento(\DateTimeInterface $fecha_tratamiento): static
    {
        $this->fecha_tratamiento = $fecha_tratamiento;

        return $this;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function setDuracion(string $duracion): static
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getCosto(): ?float
    {
        return $this->costo;
    }

    public function setCosto(float $costo): static
    {
        $this->costo = $costo;

        return $this;
    }

    /**
     * @return Collection<int, Consultas>
     */
    public function getConsultas(): Collection
    {
        return $this->consultas;
    }

    public function addConsulta(Consultas $consulta): static
    {
        if (!$this->consultas->contains($consulta)) {
            $this->consultas->add($consulta);
            $consulta->setTratamientos($this);
        }

        return $this;
    }

    public function removeConsulta(Consultas $consulta): static
    {
        if ($this->consultas->removeElement($consulta)) {
            // set the owning side to null (unless already changed)
            if ($consulta->getTratamientos() === $this) {
                $consulta->setTratamientos(null);
            }
        }

        return $this;
    }

    public function getMedicamentos(): ?medicamentos
    {
        return $this->medicamentos;
    }

    public function setMedicamentos(?medicamentos $medicamentos): static
    {
        $this->medicamentos = $medicamentos;

        return $this;
    }
}
