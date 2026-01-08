<?php

namespace App\Controller;

use App\Entity\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrincipalController extends AbstractController
{
    #[Route('/portal', name: 'portal_principal')]
    public function index(EntityManagerInterface $em): Response
    {
        // ✅ 6 productos para "Continuar comprando"
        $destacados = $em->getRepository(Producto::class)->findBy([], ['id' => 'DESC'], 6);

        return $this->render('portal.html.twig', [
            'destacados' => $destacados,
        ]);
    }
}
