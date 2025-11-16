<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comentario')]
class Comentario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "comentarios")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario;

    #[ORM\ManyToOne(targetEntity: Producto::class)]
    #[ORM\JoinColumn(name: 'producto_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Producto $producto;

    #[ORM\Column(type: 'text')]
    private string $texto;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fecha;

    #[ORM\Column(type: 'integer')]
    private int $valoracion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;
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

    public function getTexto(): string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): self
    {
        $this->texto = $texto;
        return $this;
    }

    public function getFecha(): \DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getValoracion(): int
    {
        return $this->valoracion;
    }

    public function setValoracion(int $valoracion): self
    {
        $this->valoracion = $valoracion;
        return $this;
    }
}
