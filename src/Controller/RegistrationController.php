<?php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\Usuario;
use App\Form\RegistrationFormType;  // ← AÑADIR ESTA LÍNEA
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
        $usuario = new Usuario();
        
        // ← FORMULARIO NUEVO (reemplaza el if($request->isMethod('POST')))
        $form = $this->createForm(RegistrationFormType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ← CONTRASEÑA DESDE FORMULARIO (reemplaza $request->request->get('password'))
            $plainPassword = $form->get('plainPassword')->getData();

            $hashedPassword = $passwordHasher->hashPassword($usuario, $plainPassword);
            $usuario->setPassword($hashedPassword);
            $usuario->setRoles(['ROLE_USER']);

            $em->persist($usuario);
            $em->flush();

            // ← TODO TU CÓDIGO DE EMAIL IGUAL
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

        // ← PASAR FORMULARIO A TWIG (reemplaza return $this->render('registro.html.twig'))
        return $this->render('registro.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
