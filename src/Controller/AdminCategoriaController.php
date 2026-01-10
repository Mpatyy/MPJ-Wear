<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Form\CategoriaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categorias')]
class AdminCategoriaController extends AbstractController
{
    #[Route('/', name: 'admin_categorias_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $categorias = $em->getRepository(Categoria::class)->findBy([], ['id' => 'DESC']);

        return $this->render('admin/categorias/list.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/nueva', name: 'admin_categorias_nueva')]
    public function nueva(Request $request, EntityManagerInterface $em): Response
    {
        $categoria = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categoria);
            $em->flush();

            $this->addFlash('success', 'Categoría creada correctamente.');
            return $this->redirectToRoute('admin_categorias_list');
        }

        return $this->render('admin/categorias/nueva.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
