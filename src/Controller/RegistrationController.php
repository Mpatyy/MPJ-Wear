<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registro', name: 'app_registro')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {
            $nombre   = $request->request->get('nombre');
            $email    = $request->request->get('email');
            $telefono = $request->request->get('telefono');
            $plainPassword = $request->request->get('password');

            $usuario = new Usuario();
            $usuario->setNombre($nombre);
            $usuario->setEmail($email);
            $usuario->setTelefono($telefono);

            $hashedPassword = $passwordHasher->hashPassword($usuario, $plainPassword);
            $usuario->setPassword($hashedPassword);
            $usuario->setRoles(['ROLE_USER']);

            $em->persist($usuario);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registro.html.twig');
    }
}
