<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PagoController extends AbstractController
{
    #[Route('/pago', name: 'pasarela_pago')]
    public function pasarela(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $session = $request->getSession();
        $carrito = $session->get('carrito', []);

        if (empty($carrito)) {
            $this->addFlash('error', 'No hay productos en el carrito.');
            return $this->redirectToRoute('carrito_ver');
        }

        return $this->render('pago/pasarela.html.twig');
    }

    #[Route('/pago/procesar', name: 'pago_procesar', methods: ['POST'])]
    public function procesar(Request $request): Response
    {
        // 💳 Validaciones servidor
        $numero = $request->request->get('numero_tarjeta');
        $fecha  = $request->request->get('fecha_caducidad');
        $cvv    = $request->request->get('cvv');

        if (!preg_match('/^\d{16}$/', $numero)) {
            $this->addFlash('error', 'El número de tarjeta debe tener 16 dígitos.');
            return $this->redirectToRoute('pasarela_pago');
        }

        if (!preg_match('/^\d{2}\/\d{2}$/', $fecha)) {
            $this->addFlash('error', 'Formato de fecha inválido (MM/YY).');
            return $this->redirectToRoute('pasarela_pago');
        }

        [$mes, $anio] = explode('/', $fecha);
        $anio += 2000;

        $fechaTarjeta = \DateTime::createFromFormat('Y-m', "$anio-$mes");
        $fechaActual  = new \DateTime('first day of this month');

        if (!$fechaTarjeta || $fechaTarjeta < $fechaActual) {
            $this->addFlash('error', 'La tarjeta está caducada.');
            return $this->redirectToRoute('pasarela_pago');
        }

        if (!preg_match('/^\d{3}$/', $cvv)) {
            $this->addFlash('error', 'El CVV debe tener 3 dígitos.');
            return $this->redirectToRoute('pasarela_pago');
        }

        // ✅ PAGO FICTICIO CORRECTO
        return $this->redirectToRoute('carrito_confirmar');
    }
}
