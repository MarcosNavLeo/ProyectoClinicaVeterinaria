<?php

namespace App\Entity;

use App\Repository\MascotasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: MascotasRepository::class)]
#[Broadcast]
class Mascotas
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $especie = null;

    #[ORM\Column(length: 100)]
    private ?string $raza = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha_nacimiento = null;

    #[ORM\ManyToOne(inversedBy: 'mascotas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Citas::class, mappedBy: 'mascostas')]
    private Collection $citas;

    #[ORM\Column(length: 255)]
    private ?string $foto = null;

    public function __construct()
    {
        $this->citas = new ArrayCollection();
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

    public function getEspecie(): ?string
    {
        return $this->especie;
    }

    public function setEspecie(string $especie): static
    {
        $this->especie = $especie;

        return $this;
    }

    public function getRaza(): ?string
    {
        return $this->raza;
    }

    public function setRaza(string $raza): static
    {
        $this->raza = $raza;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fecha_nacimiento): static
    {
        $this->fecha_nacimiento = $fecha_nacimiento;

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

    /**
     * @return Collection<int, Citas>
     */
    public function getCitas(): Collection
    {
        return $this->citas;
    }

    public function addCita(Citas $cita): static
    {
        if (!$this->citas->contains($cita)) {
            $this->citas->add($cita);
            $cita->setMascostas($this);
        }

        return $this;
    }

    public function removeCita(Citas $cita): static
    {
        if ($this->citas->removeElement($cita)) {
            // set the owning side to null (unless already changed)
            if ($cita->getMascostas() === $this) {
                $cita->setMascostas(null);
            }
        }

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
}
