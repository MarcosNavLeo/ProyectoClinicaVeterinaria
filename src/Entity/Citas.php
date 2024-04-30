<?php

namespace App\Entity;

use App\Repository\CitasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CitasRepository::class)]
#[Broadcast]
class Citas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hora = null;

    #[ORM\Column(length: 300)]
    private ?string $motivo = null;

    #[ORM\ManyToOne(inversedBy: 'citas')]
    private ?user $user = null;

    #[ORM\ManyToOne(inversedBy: 'citas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mascotas $mascostas = null;

    #[ORM\OneToMany(targetEntity: Consultas::class, mappedBy: 'citas')]
    private Collection $consultas;

    public function __construct()
    {
        $this->consultas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(\DateTimeInterface $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): static
    {
        $this->motivo = $motivo;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMascostas(): ?Mascotas
    {
        return $this->mascostas;
    }

    public function setMascostas(?Mascotas $mascostas): static
    {
        $this->mascostas = $mascostas;

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
            $consulta->setCitas($this);
        }

        return $this;
    }

    public function removeConsulta(Consultas $consulta): static
    {
        if ($this->consultas->removeElement($consulta)) {
            // set the owning side to null (unless already changed)
            if ($consulta->getCitas() === $this) {
                $consulta->setCitas(null);
            }
        }

        return $this;
    }
}
