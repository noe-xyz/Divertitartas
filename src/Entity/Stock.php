<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_producto = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column]
    private ?bool $agotado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProducto(): ?int
    {
        return $this->id_producto;
    }

    public function setIdProducto(int $id_producto): static
    {
        $this->id_producto = $id_producto;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function isAgotado(): ?bool
    {
        return $this->agotado;
    }

    public function setAgotado(bool $agotado): static
    {
        $this->agotado = $agotado;

        return $this;
    }
}
