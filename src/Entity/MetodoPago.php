<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'metodo_pago')]
class MetodoPago
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $tipo;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $proveedor = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $datos = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;
        return $this;
    }

    public function getProveedor(): ?string
    {
        return $this->proveedor;
    }

    public function setProveedor(?string $proveedor): self
    {
        $this->proveedor = $proveedor;
        return $this;
    }

    public function getDatos(): ?string
    {
        return $this->datos;
    }

    public function setDatos(?string $datos): self
    {
        $this->datos = $datos;
        return $this;
    }
}
