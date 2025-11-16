<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pedido_producto')]
class PedidoProducto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pedidos::class)]
    private ?Pedidos $pedido = null;

    #[ORM\ManyToOne(targetEntity: Producto::class)]
    private ?Producto $producto = null;

    #[ORM\Column(type: 'integer')]
    private int $cantidad;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $precioUnitario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedido(): ?Pedidos
    {
        return $this->pedido;
    }

    public function setPedido(?Pedidos $pedido): self
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;
        return $this;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;
        return $this;
    }

    public function getPrecioUnitario(): string
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(string $precioUnitario): self
    {
        $this->precioUnitario = $precioUnitario;
        return $this;
    }
}
