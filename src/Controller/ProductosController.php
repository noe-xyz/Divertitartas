<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductosController extends AbstractController
{
    #[Route('/productos', name: 'productos')]
    public function productos(): Response
    {
        return $this->render('productos/productos.html.twig');
    }

    #[Route('/productos/{categoria}', name: 'categorias', requirements: ['categoria' => 'tartas-creativas|preferencias|mas-productos'])]
    public function mostrarProductos(string $categoria, ProductoRepository $repository): Response
    {
        $productosMostrados = $repository->findBy(['categoria' => $categoria]);
//        $this->crearSlug($productosMostrados, $entityManager, $slugger);
        $mensajes = [];
        switch ($categoria) {
            case 'tartas-creativas':
                $mensajes = [
                    'titulo' => 'Tartas Creativas',
                    'subtitulo' => 'Personalizadas según tus ideas, destacan por sus diseños únicos y acabados impecables,
                perfectos para cualquier ocasión especial. ¡Cada tarta es una experiencia visual y deliciosa!',
                ];
                break;
            case 'preferencias':
                $mensajes = [
                    'titulo' => 'Especiales con Preferencias Alimenticias',
                    'subtitulo' => 'Nuestras tartas especiales, sin gluten, veganas o sin lactosa, están hechas para que todos disfruten. ¡Sabores irresistibles para cada necesidad!',
                ];
                break;
            case 'mas-productos':
                $mensajes = [
                    'titulo' => 'Nos encanta el dulce',
                    'subtitulo' => 'Todos nuestros dulces son deliciosos y están elaborados con cariño y dedicación. ¡Especiales para Ti!'
                ];
        }

        return $this->render('productos/categorias-productos.html.twig', [
            'categoria' => $categoria,
            'productosMostrados' => $productosMostrados,
            'mensajes' => $mensajes,
        ]);
    }

    #[Route('/productos/{categoria}/{nombre}', name: 'producto')]
    public function mostrarUnProducto($nombre, ProductoRepository $repository): Response
    {
        #TODO esto está cogiendo bien el nombre de la URL?
        $productoAMostrar = $repository->findOneByNombre($nombre);
        if (!$productoAMostrar) {
            throw $this->createNotFoundException('No encontramos esta página...');
        }

        return $this->render('productos/detalle-producto.html.twig', [
            'nombre' => $productoAMostrar->getNombre(),
        ]);
    }

#TODO mover al lugar donde se crearán las tartas
//    private function crearSlug(EntityManagerInterface $entityManager, SluggerInterface $slugger): string
//    {
//        $slug = $slugger->slug($producto->getNombre())->lower();
//        $producto->setSlug($slug);
//        $entityManager->persist($producto);
//        $entityManager->flush();
//    }


}