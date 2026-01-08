<?php

namespace App\Controller;

use App\Repository\ProductosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProductosRepository $repo): Response
    {
        // ✅ “Destacados”: últimos productos (ajusta el número si quieres)
        $destacados = $repo->findBy([], ['id' => 'DESC'], 8);

        return $this->render('home/index.html.twig', [
            'destacados' => $destacados,
        ]);
    }
}
