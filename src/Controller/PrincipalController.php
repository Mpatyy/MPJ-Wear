<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrincipalController extends AbstractController
{
    #[Route('/portal', name: 'portal_principal')]
    public function index(): Response
    {
        return $this->render('portal.html.twig');
    }
}
