<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'carrito')]
class Carrito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class , inversedBy: "carritos")]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $creadoEn;

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

    public function getCreadoEn(): \DateTimeInterface
    {
        return $this->creadoEn;
    }

    public function setCreadoEn(\DateTimeInterface $creadoEn): self
    {
        $this->creadoEn = $creadoEn;
        return $this;
    }

}
