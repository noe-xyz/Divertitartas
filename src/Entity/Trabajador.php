<?php

namespace App\Entity;

use App\Repository\TrabajadorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrabajadorRepository::class)]
class Trabajador extends Usuario
{
    #[ORM\Column(length: 40)]
    private ?string $turno = null;

    #[ORM\Column(length: 30)]
    private ?string $puesto = null;

    public function getTurno(): ?string
    {
        return $this->turno;
    }

    public function setTurno(string $turno): static
    {
        $this->turno = $turno;

        return $this;
    }

    public function getPuesto(): ?string
    {
        return $this->puesto;
    }

    public function setPuesto(string $puesto): static
    {
        $this->puesto = $puesto;

        return $this;
    }
}
