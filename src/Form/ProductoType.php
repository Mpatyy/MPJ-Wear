<?php
namespace App\Form;

use App\Entity\Producto;
use App\Entity\Categoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',   // o el campo que uses en Categoria
                'placeholder' => 'Selecciona una categoría',
                'required' => true,
                'label' => 'Categoría',
            ])
            ->add('imagen', FileType::class, [
                'label' => 'Imagen de portada',
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}

