<?php

namespace App\Controller;

use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// El prefijo de la ruta base para todas las APIs relacionadas con el buscador
#[Route('/api', name: 'api_')]
class ApiBuscadorController extends AbstractController
{
    /**
     * Endpoint para el buscador dinámico de React.
     * * Recibe los filtros por GET y devuelve una lista de productos en formato JSON.
     * La URL a la que React hace la petición es: /api/buscar-productos
     */
    #[Route('/buscar-productos', name: 'buscar_productos', methods: ['GET'])]
    public function buscar(Request $request, ProductosRepository $repo): JsonResponse
    {
        // 1. Recogemos los parámetros que envía React
        // React envía: q, talla, color, precio
        $texto = $request->query->get('q', '');       // Nombre o descripción
        $talla = $request->query->get('talla');       // Talla
        $color = $request->query->get('color');       // Color
        $precio = $request->query->get('precio');     // Precio Máximo

        // 2. Llamamos al repositorio con los filtros
        // Asegúrate de que tu método buscarFiltros() en ProductosRepository.php acepta estos 4 parámetros
        $productos = $repo->buscarFiltros($texto, $talla, $color, $precio);

        // 3. Convertimos los objetos Doctrine a un Array JSON simple
        $data = [];
        foreach ($productos as $p) {
            $data[] = [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'precio' => $p->getPrecio(),
                // Si la imagen puede ser null, protegemos la llamada
                'imagen' => $p->getImagen() ? $p->getImagen() : null, 
                'talla' => $p->getTalla(),
                'color' => $p->getColor()
                // Si tienes otros campos necesarios (ej. slug, categoria), añádelos aquí.
            ];
        }

        // 4. Devolvemos la respuesta JSON
        return new JsonResponse($data);
    }
}