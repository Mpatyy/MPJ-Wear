<?php

namespace App\Form;

use App\Entity\Tarjeta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class TarjetaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, [
                'label' => 'Número de tarjeta',
                'attr' => [
                    'class' => 'form-control',
                    'inputmode' => 'numeric',
                    'autocomplete' => 'cc-number',
                    'placeholder' => '1234 5678 9012 3456',
                    'maxlength' => 19, // 16 + 3 espacios
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Introduce el número de tarjeta.']),
                    new Regex([
                        'pattern' => '/^\d{16}$|^\d{4}\s\d{4}\s\d{4}\s\d{4}$/',
                        'message' => 'El número debe tener 16 dígitos.',
                    ]),
                ],
            ])

            // ✅ Caducidad como texto MM/AAAA (siempre igual en cualquier navegador)
            // mapped=false porque lo convertimos nosotros a DateTime y lo metemos en la entidad
            ->add('caducidad_texto', TextType::class, [
                'label' => 'Caducidad (MM/AAAA)',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'inputmode' => 'numeric',
                    'autocomplete' => 'cc-exp',
                    'placeholder' => '05/2028',
                    'maxlength' => 7,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Introduce la caducidad.']),
                    new Regex([
                        'pattern' => '/^(0[1-9]|1[0-2])\/\d{4}$/',
                        'message' => 'Formato inválido. Usa MM/AAAA (ej: 05/2028).',
                    ]),
                ],
            ])

            ->add('cvv', TextType::class, [
                'label' => 'CVV',
                'attr' => [
                    'class' => 'form-control',
                    'inputmode' => 'numeric',
                    'autocomplete' => 'cc-csc',
                    'placeholder' => '123',
                    'maxlength' => 3,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Introduce el CVV.']),
                    new Length([
                        'min' => 3,
                        'max' => 3,
                        'minMessage' => 'El CVV debe tener 3 dígitos.',
                        'maxMessage' => 'El CVV debe tener 3 dígitos.',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{3}$/',
                        'message' => 'El CVV debe tener 3 dígitos.',
                    ]),
                ],
            ]);

        // ✅ Convertimos "MM/AAAA" a DateTime y lo guardamos en $tarjeta->setCaducidad()
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Tarjeta $tarjeta */
            $tarjeta = $event->getData();
            $form = $event->getForm();

            $cadText = (string) $form->get('caducidad_texto')->getData();
            $cadText = trim($cadText);

            if ($cadText === '') {
                return;
            }

            // Ya viene validado por regex, pero lo parseamos seguro
            [$mes, $anio] = explode('/', $cadText);
            $mes = (int) $mes;
            $anio = (int) $anio;

            if ($mes < 1 || $mes > 12 || $anio < 2000 || $anio > 2100) {
                $form->get('caducidad_texto')->addError(new FormError('Caducidad inválida.'));
                return;
            }

            // Guardamos como primer día del mes
            $fecha = \DateTimeImmutable::createFromFormat('Y-m-d', sprintf('%04d-%02d-01', $anio, $mes));
            if (!$fecha) {
                $form->get('caducidad_texto')->addError(new FormError('No se pudo interpretar la fecha.'));
                return;
            }

            // (Opcional) evitar caducadas
            $fechaActual = new \DateTimeImmutable('first day of this month');
            if ($fecha < $fechaActual) {
                $form->get('caducidad_texto')->addError(new FormError('La tarjeta está caducada.'));
                return;
            }

            $tarjeta->setCaducidad($fecha);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarjeta::class,
        ]);
    }
}
