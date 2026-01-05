<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Producto;
use App\Entity\Categoria;

class ProductoController extends AbstractController
{
    #[Route('/productos', name: 'producto_listado')]
    public function listado(Request $request, EntityManagerInterface $entityManager): Response
    {
        // categoría por querystring: /productos?categoria=camisetas
        $slug = $request->query->get('categoria');

        $categorias = $entityManager->getRepository(Categoria::class)->findBy([], ['nombre' => 'ASC']);

        $categoriaActiva = null;
        if (!empty($slug)) {
            $categoriaActiva = $entityManager->getRepository(Categoria::class)->findOneBy(['slug' => $slug]);
        }

        // Si el slug no existe, lo tratamos como "ver todo"
        if ($slug && !$categoriaActiva) {
            $slug = null;
        }

        $productos = $categoriaActiva
            ? $entityManager->getRepository(Producto::class)->findBy(['categoria' => $categoriaActiva], ['id' => 'DESC'])
            : $entityManager->getRepository(Producto::class)->findBy([], ['id' => 'DESC']);

        return $this->render('producto/listado.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
            'slugActiva' => $slug,
        ]);
    }

    #[Route('/producto/{id}', name: 'producto_detalle')]
    public function detalle(EntityManagerInterface $entityManager, int $id): Response
    {
        $producto = $entityManager->getRepository(Producto::class)->find($id);
        if (!$producto) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        $variaciones = $producto->getVariaciones();

        // Mapa opcional para pintar los swatches
        $mapaHex = [
            'negro' => '#111111',
            'blanco' => '#F5F5F5',
            'gris' => '#9CA3AF',
            'azul' => '#1D4ED8',
            'azul marino' => '#0B1E3B',
            'beige' => '#D6C5A1',
            'rojo' => '#DC2626',
            'verde' => '#16A34A',
            'marron' => '#7C4A2D',
            'marrón' => '#7C4A2D',
        ];

        // Imagen inicial: primera variación con imagen, si no la del producto
        $imagenInicial = $producto->getImagen();
        foreach ($variaciones as $v) {
            if ($v->getImagen()) {
                $imagenInicial = $v->getImagen();
                break;
            }
        }

        // Colores únicos (cada color con su imagen y su hex opcional)
        $colores = [];
        foreach ($variaciones as $v) {
            $color = $v->getColor();
            if (!$color) continue;

            $key = mb_strtolower(trim($color));

            if (!isset($colores[$key])) {
                $colores[$key] = [
                    'color' => $color,
                    'imagen' => $v->getImagen(), // imagen por color
                    'hex' => $mapaHex[$key] ?? null,
                ];
            }
        }

        return $this->render('producto/detalle.html.twig', [
            'producto' => $producto,
            'variaciones' => $variaciones,
            'colores' => array_values($colores),
            'imagenInicial' => $imagenInicial,
        ]);
    }
}
