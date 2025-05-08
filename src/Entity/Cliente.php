<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#TODO: abstract entity de la que hereden cliente y empresa????
#[ORM\Entity(repositoryClass: ClienteRepository::class)]
#[UniqueEntity('email', message: 'Este email ya existe.')]
class Cliente extends Usuario
{
    #Atributos del objeto
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apellido1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apellido2 = null;
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $fecha_nacimiento = null;

    #[ORM\Column(type: 'json', length: 255, nullable: true)]
    private ?array $domicilio = null;

    #[ORM\Column(nullable: true)]
    private ?int $telefono2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $puntos = null;

    #[ORM\Column(nullable: true)]
    private ?string $sexo = null;

    #Getters y setters
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getApellido1(): ?string
    {
        return $this->apellido1;
    }

    public function setApellido1(?string $apellido1): static
    {
        $this->apellido1 = $apellido1;
        return $this;
    }

    public function getApellido2(): ?string
    {
        return $this->apellido2;
    }

    public function setApellido2(?string $apellido2): static
    {
        $this->apellido2 = $apellido2;
        return $this;
    }

    public function getFechaNacimiento(): ?\DateTime
    {
        return $this->fecha_nacimiento;
    }

    public function setFechaNacimiento(?\DateTime $fecha_nacimiento): static
    {
        $this->fecha_nacimiento = $fecha_nacimiento;
        return $this;
    }

    public function getDomicilio(): ?array
    {
        return $this->domicilio;
    }

    public function setDomicilio(?array $domicilio): static
    {
        $this->domicilio = $domicilio;
        return $this;
    }

    public function getTelefono2(): ?int
    {
        return $this->telefono2;
    }

    public function setTelefono2(?int $telefono2): static
    {
        $this->telefono2 = $telefono2;
        return $this;
    }

    public function getPuntos(): ?int
    {
        return $this->puntos;
    }

    public function setPuntos(?int $puntos): static
    {
        $this->puntos = $puntos;
        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }
    public function setSexo(?string $sexo): static
    {
        $this->sexo = $sexo;
        return $this;
    }
}
