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

    // Aquí guardas lo que quieras (JSON recomendado)
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

    /**
     * ✅ Getter “virtual” para Twig: tarjeta.numero
     * Busca en datos (JSON) o intenta sacar dígitos si es string.
     */
    public function getNumero(): ?string
    {
        if (!$this->datos) return null;

        // Intentar JSON
        $json = json_decode($this->datos, true);
        if (is_array($json)) {
            if (!empty($json['numero'])) return (string) $json['numero'];
            if (!empty($json['numero_tarjeta'])) return (string) $json['numero_tarjeta'];
        }

        // Fallback: extraer dígitos de la cadena completa
        $soloDigitos = preg_replace('/\D+/', '', $this->datos);
        return $soloDigitos !== '' ? $soloDigitos : null;
    }

    /**
     * ✅ Getter “virtual” para Twig: tarjeta.caducidad
     * Devuelve algo tipo "MM/YY".
     */
    public function getCaducidad(): ?string
    {
        if (!$this->datos) return null;

        $json = json_decode($this->datos, true);
        if (is_array($json)) {
            $v = $json['caducidad'] ?? $json['fecha_caducidad'] ?? null;

            if ($v) {
                $v = (string) $v;

                // YYYY-MM -> MM/YY
                if (preg_match('/^\d{4}\-\d{2}$/', $v)) {
                    $anio = substr($v, 0, 4);
                    $mes  = substr($v, 5, 2);
                    return $mes . '/' . substr($anio, 2, 2);
                }

                // MMYY -> MM/YY
                if (preg_match('/^\d{4}$/', $v)) {
                    return substr($v, 0, 2) . '/' . substr($v, 2, 2);
                }

                // MM/YY (ya ok)
                if (preg_match('/^\d{2}\/\d{2}$/', $v)) {
                    return $v;
                }

                return $v;
            }
        }

        return null;
    }
}
