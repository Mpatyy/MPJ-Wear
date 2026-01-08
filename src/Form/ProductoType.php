<?php

namespace App\Form;

use App\Entity\Producto;
use App\Form\ProductoVariacionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del producto',
            ])
            ->add('descripcion', TextType::class, [
                'label' => 'Descripción',
            ])
            ->add('precio', MoneyType::class, [
                'label' => 'Precio',
                'currency' => 'EUR',
            ])
            ->add('imagen', FileType::class, [
                'label' => 'Imagen de portada',
                'mapped' => false,
                'required' => false,
            ])
            ->add('variaciones', CollectionType::class, [
                'entry_type' => ProductoVariacionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
