<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lineas_pedido')]
class LineaPedido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pedidos::class, inversedBy: 'lineas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pedidos $pedido = null;


    #[ORM\ManyToOne(targetEntity: Producto::class)]
    #[ORM\JoinColumn(name: 'producto_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private Producto $producto;

    #[ORM\Column(type: 'string', length: 10)]
    private string $talla;

    #[ORM\Column(type: 'string', length: 30)]
    private string $color;

    #[ORM\Column(type: 'integer')]
    private int $cantidad;

    #[ORM\Column(name: 'precio_unitario', type: 'decimal', precision: 10, scale: 2)]
    private string $precioUnitario;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $subtotal;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imagen = null;

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedido(): Pedidos
    {
        return $this->pedido;
    }

    public function setPedido(Pedidos $pedido): self
    {
        $this->pedido = $pedido;
        return $this;
    }

    public function getProducto(): Producto
    {
        return $this->producto;
    }

    public function setProducto(Producto $producto): self
    {
        $this->producto = $producto;
        return $this;
    }

    public function getTalla(): string
    {
        return $this->talla;
    }

    public function setTalla(string $talla): self
    {
        $this->talla = $talla;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
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

    public function getSubtotal(): string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): self
    {
        $this->subtotal = $subtotal;
        return $this;
    }
}
