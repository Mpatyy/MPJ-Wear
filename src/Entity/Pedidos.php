<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'pedidos')]
class Pedidos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "pedidos")]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Usuario $usuario;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $fecha;

    #[ORM\Column(type: 'string', length: 30)]
    private string $estado;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total;

    #[ORM\ManyToOne(targetEntity: Direccion::class)]
    #[ORM\JoinColumn(name: 'direccion_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Direccion $direccion = null;

    #[ORM\ManyToOne(targetEntity: MetodoPago::class)]
    #[ORM\JoinColumn(name: 'metodo_pago_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?MetodoPago $metodoPago = null;

    #[ORM\OneToMany(mappedBy: 'pedido', targetEntity: LineaPedido::class, cascade: ['persist', 'remove'])]
    private Collection $lineas;

    public function __construct()
    {
        $this->lineas = new ArrayCollection();
    }

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

    public function getFecha(): \DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;
        return $this;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getDireccion(): ?Direccion
    {
        return $this->direccion;
    }

    public function setDireccion(?Direccion $direccion): self
    {
        $this->direccion = $direccion;
        return $this;
    }

    public function getMetodoPago(): ?MetodoPago
    {
        return $this->metodoPago;
    }

    public function setMetodoPago(?MetodoPago $metodoPago): self
    {
        $this->metodoPago = $metodoPago;
        return $this;
    }

        public function getLineas(): Collection
    {
        return $this->lineas;
    }

    public function addLinea(LineaPedido $linea): self
    {
        if (!$this->lineas->contains($linea)) {
            $this->lineas[] = $linea;
            $linea->setPedido($this);
        }

        return $this;
    }

    public function removeLinea(LineaPedido $linea): self
    {
        if ($this->lineas->removeElement($linea)) {
            if ($linea->getPedido() === $this) {
                $linea->setPedido(null);
            }
        }

        return $this;
    }
}
