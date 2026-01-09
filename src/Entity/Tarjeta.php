<?php

namespace App\Entity;

use App\Repository\TarjetaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Usuario;

#[ORM\Entity(repositoryClass: TarjetaRepository::class)]
class Tarjeta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'tarjetas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $user = null;

    #[ORM\Column(length: 20)]
    private ?string $numero = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $caducidad = null;

    #[ORM\Column(length: 4)]
    private ?string $cvv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Usuario
    {
        return $this->user;
    }

    public function setUser(Usuario $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function getCaducidad(): ?\DateTimeInterface
    {
        return $this->caducidad;
    }

    public function setCaducidad(?\DateTimeInterface $caducidad): self
    {
        $this->caducidad = $caducidad;
        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;
        return $this;
    }
}
