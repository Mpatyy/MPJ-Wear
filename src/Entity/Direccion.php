<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'direccion')]
class Direccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "direcciones")]
    private ?Usuario $usuario = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $calle;

    #[ORM\Column(type: 'string', length: 50)]
    private string $ciudad;

    #[ORM\Column(type: 'string', length: 10)]
    private string $cp;

    #[ORM\Column(type: 'string', length: 50)]
    private string $provincia;

    #[ORM\Column(type: 'string', length: 50)]
    private string $pais;

    #[ORM\Column(type: 'string', length: 50)]
    private string $tipo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getCalle(): string
    {
        return $this->calle;
    }

    public function setCalle(string $calle): self
    {
        $this->calle = $calle;
        return $this;
    }

    public function getCiudad(): string
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad): self
    {
        $this->ciudad = $ciudad;
        return $this;
    }

    public function getCp(): string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;
        return $this;
    }

    public function getProvincia(): string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;
        return $this;
    }

    public function getPais(): string
    {
        return $this->pais;
    }

    public function setPais(string $pais): self
    {
        $this->pais = $pais;
        return $this;
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
}