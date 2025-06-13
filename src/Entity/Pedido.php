<?php

namespace App\Entity;

use App\Repository\PedidoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PedidoRepository::class)]
class Pedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $fecha = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\Column]
    private ?float $coste_total = null;

    #[ORM\Column]
    private ?int $id_cliente = null;

    #[ORM\OneToMany(mappedBy: 'pedido', targetEntity: DetallesPedido::class, cascade: ['persist', 'remove'])]
    private Collection $detalles;

    public function __construct()
    {
        $this->detalles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCosteTotal(): ?float
    {
        return $this->coste_total;
    }

    public function setCosteTotal(float $coste_total): static
    {
        $this->coste_total = $coste_total;

        return $this;
    }

    public function getIdCliente(): ?int
    {
        return $this->id_cliente;
    }

    public function setIdCliente(int $id_cliente): static
    {
        $this->id_cliente = $id_cliente;

        return $this;
    }

    public function getDetalles(): Collection
    {
        return $this->detalles;
    }

    public function addDetalle(DetallesPedido $detalle): static
    {
        if (!$this->detalles->contains($detalle)) {
            $this->detalles[] = $detalle;
            $detalle->setPedido($this);
        }
        return $this;
    }

    public function removeDetalle(DetallesPedido $detalle): static
    {
        if ($this->detalles->removeElement($detalle)) {
            if ($detalle->getPedido() === $this) {
                $detalle->setPedido(null);
            }
        }
        return $this;
    }
}
