<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#TODO: abstract entity de la que hereden cliente y empresa????
#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
#[UniqueEntity('email', message: 'Este email ya existe.')]
#[UniqueEntity(fields: ['nombreEmpresa','nifCif'], message: 'Ya existe una empresa registrada con esos datos.')]
class Empresa extends Usuario
{
    #Atributos del objeto
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apellido1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apellido2 = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nombreEmpresa = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nifCif = null;

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

    public function getNombreEmpresa(): ?string
    {
        return $this->nombreEmpresa;
    }

    public function setNombreEmpresa(?string $nombreEmpresa): static
    {
        $this->nombreEmpresa = $nombreEmpresa;
        return $this;
    }

    public function getNifCif(): ?string
    {
        return $this->nifCif;
    }

    public function setNifCif(?string $nifCif): static
    {
        $this->nifCif = $nifCif;
        return $this;
    }
}
