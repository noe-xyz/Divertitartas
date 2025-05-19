<?php

namespace App\Entity;

use App\Repository\DescuentoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DescuentoRepository::class)]
class Descuento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $codigo_descuento = null;

    #[ORM\Column(length: 30)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?float $cantidad_descontada = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigoDescuento(): ?string
    {
        return $this->codigo_descuento;
    }

    public function setCodigoDescuento(string $codigo_descuento): static
    {
        $this->codigo_descuento = $codigo_descuento;

        return $this;
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

    public function getCantidadDescontada(): ?float
    {
        return $this->cantidad_descontada;
    }

    public function setCantidadDescontada(float $cantidad_descontada): static
    {
        $this->cantidad_descontada = $cantidad_descontada;

        return $this;
    }
}
