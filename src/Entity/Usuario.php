<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "tipo_usuario", type: "string")]
#[ORM\DiscriminatorMap([
    "usuario" => Usuario::class,
    "empresa" => Empresa::class,
    "cliente" => Cliente::class,
])]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreCompleto = null;

    #[ORM\Column(nullable: true)]
    private ?int $telefono1 = null;

    #[ORM\Column(type: 'json')]
    private ?array $roles = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getNombreCompleto(): ?string
    {
        return $this->nombreCompleto;
    }

    public function setNombreCompleto(string $nombreCompleto): static
    {
        $this->nombreCompleto = $nombreCompleto;

        return $this;
    }

    public function getTelefono1(): ?int
    {
        return $this->telefono1;
    }

    public function setTelefono1(?int $telefono1): static
    {
        $this->telefono1 = $telefono1;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }
    public function setRoles(?array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
}
