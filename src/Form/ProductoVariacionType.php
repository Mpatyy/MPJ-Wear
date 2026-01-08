<?php

namespace App\Form;

use App\Entity\ProductoVariacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoVariacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('talla', TextType::class)
            ->add('color', TextType::class)
            ->add('stock', IntegerType::class)
            ->add('imagen', FileType::class, [
                'label' => 'Imagen de la variación',
                'required' => false,
                'mapped' => false, // Symfony no mapea automáticamente FileType
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductoVariacion::class,
        ]);
    }
}
