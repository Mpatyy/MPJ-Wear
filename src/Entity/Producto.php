<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'productos')]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    private string $nombre;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $precio = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\ManyToOne(targetEntity: Categoria::class, inversedBy: 'productos')]
    #[ORM\JoinColumn(name: 'categoria_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Categoria $categoria = null;

    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: ProductoVariacion::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $variaciones;

    public function __construct()
    {
        $this->variaciones = new ArrayCollection();
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(?string $precio): self
    {
        $this->precio = $precio;
        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): self
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getVariaciones(): Collection
    {
        return $this->variaciones;
    }

    public function addVariacion(ProductoVariacion $variacion): self
    {
        if (!$this->variaciones->contains($variacion)) {
            $this->variaciones->add($variacion);
            $variacion->setProducto($this);
        }

        return $this;
    }

    public function removeVariacion(ProductoVariacion $variacion): self
    {
        if ($this->variaciones->removeElement($variacion)) {
            // orphanRemoval=true se encarga del borrado si ya no está referenciado
            if ($variacion->getProducto() === $this) {
                $variacion->setProducto(null);
            }
        }

        return $this;
    }
}
