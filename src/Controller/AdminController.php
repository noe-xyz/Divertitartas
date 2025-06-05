<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\DetallesPedido;
use App\Entity\Pedido;
use App\Entity\Proveedores;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
//    #[Route('/comunicados', name: 'admin')]
//    public function admin(): Response
//    {
//        return $this->render('admin/admin.html.twig');
//    }

    #[Route('/administracion/{accion}', name: 'mostrar-accion', requirements: ['accion' => 'comunicados|usuarios|proveedores|contabilidad|pedidos|stock|configuracion'])]
    public function mostrarAccion(string $accion, Session $session, EntityManagerInterface $entityManager): Response
    {
        $idUsuarioRegistrado = $session->get('id');
        $usuarioRegistrado = $entityManager->getRepository(Usuario::class)->findOneBy(['id' => $idUsuarioRegistrado]);

        if (get_class($usuarioRegistrado) !== Trabajador::class) {
            return $this->redirectToRoute('index');
        }
//        $idUsuarioRegistrado = $session->get('id');
//        $usuarioRegistrado = $usuarioRepository->findOneById($idUsuarioRegistrado);
//        if ($usuarioRegistrado->getPuesto() == "administrador") {
//            $isAdmin=true;
//        }else{
//            $isAdmin=false;
//        }
        $clientesRegistrados = $entityManager->getRepository(Cliente::class)->findAll();
        $trabajadoresRegistrados = $entityManager->getRepository(Trabajador::class)->findAll();
        $proveedores = $entityManager->getRepository(Proveedores::class)->findAll();
        $pedidos = $entityManager->getRepository(Pedido::class)->findAll();
        $detallesPedido = $entityManager->getRepository(DetallesPedido::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'accion' => $accion,
//            'isAdmin' => $isAdmin,
            'clientesRegistrados' => $clientesRegistrados,
            'trabajadoresRegistrados' => $trabajadoresRegistrados,
            'proveedores' => $proveedores,
            'pedidos' => $pedidos,
            'detallesPedido' => $detallesPedido,
        ]);
    }
}