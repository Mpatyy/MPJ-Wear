<?php
// src/Form/RegistrationFormType.php
namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new Assert\Email(['message' => 'Email inválido'])]
            ])
            ->add('telefono', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'El teléfono es obligatorio']),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{9}$/',
                        'message' => 'El teléfono debe tener exactamente 9 dígitos numéricos',
                    ]),
                ],
                'attr' => [
                    'class'     => 'form-control',
                    'maxlength' => 9,
                    'pattern'   => '\d{9}',
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Contraseña',
                    'attr' => [
                        'id' => 'registration_plainPassword_first',  // ← ID para JS
                        'class' => 'form-control'
                    ],
                ],
                'second_options' => [
                    'label' => 'Repetir contraseña',
                    'attr' => ['id' => 'password2']
                ],
                'invalid_message' => 'Las contraseñas deben coincidir',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Contraseña obligatoria']),
                    new Assert\Length(['min' => 8, 'minMessage' => 'Mínimo 8 caracteres']),
                    new Assert\Regex(['pattern' => '/[a-z]/', 'message' => 'Al menos 1 minúscula']),
                    new Assert\Regex(['pattern' => '/[A-Z]/', 'message' => 'Al menos 1 mayúscula']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
