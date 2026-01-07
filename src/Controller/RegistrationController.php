<?php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
        MailerInterface $mailer,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {
            $nombre = $request->request->get('nombre');
            $emailUsuario = $request->request->get('email');
            $telefono = $request->request->get('telefono');
            $plainPassword = $request->request->get('password');

            $usuario = new Usuario();
            $usuario->setNombre($nombre);
            $usuario->setEmail($emailUsuario);
            $usuario->setTelefono($telefono);

            $hashedPassword = $passwordHasher->hashPassword($usuario, $plainPassword);
            $usuario->setPassword($hashedPassword);
            $usuario->setRoles(['ROLE_USER']);

            $em->persist($usuario);
            $em->flush();

            // Enviar email de bienvenida
            $mensaje = (new TemplatedEmail())
                ->from('no-reply@mpj-wear.com')
                ->to($usuario->getEmail())
                ->subject('Bienvenido a MPJ WEAR')
                ->htmlTemplate('email_registro.html.twig')
                ->context([
                    'nombre' => $usuario->getNombre(),
                    'emailUsuario' => $usuario->getEmail(),
                ]);


            $mailer->send($mensaje);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registro.html.twig');
    }
}
