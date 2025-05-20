<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?float $precio = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $sabor = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $relleno = null;

    #[ORM\Column(nullable: true)]
    private ?int $raciones = null;

    #[ORM\Column(length: 255)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comentario = null;

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

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getSabor(): ?string
    {
        return $this->sabor;
    }

    public function setSabor(?string $sabor): static
    {
        $this->sabor = $sabor;

        return $this;
    }

    public function getRelleno(): ?string
    {
        return $this->relleno;
    }

    public function setRelleno(?string $relleno): static
    {
        $this->relleno = $relleno;

        return $this;
    }

    public function getRaciones(): ?int
    {
        return $this->raciones;
    }

    public function setRaciones(?int $raciones): static
    {
        $this->raciones = $raciones;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): static
    {
        $this->comentario = $comentario;

        return $this;
    }
}
