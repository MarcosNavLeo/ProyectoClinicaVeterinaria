<?php

namespace App\Entity;

use App\Repository\MedicamentosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: MedicamentosRepository::class)]
#[Broadcast]
class Medicamentos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $instrucciones = null;

    #[ORM\Column(length: 255)]
    private ?string $foto = null;

    #[ORM\Column(length: 255)]
    private ?string $dosis = null;

    #[ORM\OneToMany(targetEntity: Tratamientos::class, mappedBy: 'medicamentos')]
    private Collection $tratamientos;

    public function __construct()
    {
        $this->tratamientos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getInstrucciones(): ?string
    {
        return $this->instrucciones;
    }

    public function setInstrucciones(string $instrucciones): static
    {
        $this->instrucciones = $instrucciones;

        return $this;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): static
    {
        $this->foto = $foto;

        return $this;
    }

    public function getDosis(): ?string
    {
        return $this->dosis;
    }

    public function setDosis(string $dosis): static
    {
        $this->dosis = $dosis;

        return $this;
    }

    /**
     * @return Collection<int, Tratamientos>
     */
    public function getTratamientos(): Collection
    {
        return $this->tratamientos;
    }

    public function addTratamiento(Tratamientos $tratamiento): static
    {
        if (!$this->tratamientos->contains($tratamiento)) {
            $this->tratamientos->add($tratamiento);
            $tratamiento->setMedicamentos($this);
        }

        return $this;
    }

    public function removeTratamiento(Tratamientos $tratamiento): static
    {
        if ($this->tratamientos->removeElement($tratamiento)) {
            // set the owning side to null (unless already changed)
            if ($tratamiento->getMedicamentos() === $this) {
                $tratamiento->setMedicamentos(null);
            }
        }

        return $this;
    }
}
