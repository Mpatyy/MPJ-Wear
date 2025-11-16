<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'usuarios')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nombre;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(name: 'password', type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'string', length: 11, unique: true)]
    private string $telefono;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Carrito::class)]
    private Collection $carritos;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Comentario::class)]
    private Collection $comentarios;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Direccion::class)]
    private Collection $direcciones;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: Pedidos::class)]
    private Collection $pedidos;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    public function __construct()
    {
        $this->carritos = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
        $this->direcciones = new ArrayCollection();
        $this->pedidos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;
        return $this;
    }

    // Métodos para colecciones

    public function getCarritos(): Collection
    {
        return $this->carritos;
    }
    public function addCarrito(Carrito $carrito): self
    {
        if (!$this->carritos->contains($carrito)) {
            $this->carritos[] = $carrito;
            $carrito->setUsuario($this);
        }
        return $this;
    }

    public function removeCarrito(Carrito $carrito): self
    {
        if ($this->carritos->removeElement($carrito)) {
            if ($carrito->getUsuario() === $this) {
                $carrito->setUsuario(null);
            }
        }
        return $this;
    }
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): self
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios[] = $comentario;
            $comentario->setUsuario($this);
        }
        return $this;
    }

    public function removeComentario(Comentario $comentario): self
    {
        if ($this->comentarios->removeElement($comentario)) {
            if ($comentario->getUsuario() === $this) {
                $comentario->setUsuario(null);
            }
        }
        return $this;
    }
    public function getDirecciones(): Collection
    {
        return $this->direcciones;
    }

    public function addDireccion(Direccion $direccion): self
    {
        if (!$this->direcciones->contains($direccion)) {
            $this->direcciones[] = $direccion;
            $direccion->setUsuario($this);
        }
        return $this;
    }
    public function removeDireccion(Direccion $direccion): self
    {
        if ($this->direcciones->removeElement($direccion)) {
            if ($direccion->getUsuario() === $this) {
                $direccion->setUsuario(null);
            }
        }
        return $this;
    }

    public function getPedidos(): Collection
    {
        return $this->pedidos;
    }

    public function addPedido(Pedidos $pedido): self
    {
        if (!$this->pedidos->contains($pedido)) {
            $this->pedidos[] = $pedido;
            $pedido->setUsuario($this);
        }
        return $this;
    }

    public function removePedido(Pedidos $pedido): self
    {
        if ($this->pedidos->removeElement($pedido)) {
            if ($pedido->getUsuario() === $this) {
                $pedido->setUsuario(null);
            }
        }
        return $this;
    }

    // Métodos de UserInterface y PasswordAuthenticatedUserInterface

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Si tienes datos sensibles temporales, límpialos aquí
    }
}
