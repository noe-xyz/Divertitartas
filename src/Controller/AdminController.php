<?php

namespace App\Controller;

use App\Repository\PedidoRepository;
use App\Repository\ProveedoresRepository;
use App\Repository\UsuarioRepository;
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
    public function mostrarAccion(string $accion, UsuarioRepository $usuarioRepository, ProveedoresRepository $proveedoresRepository, PedidoRepository $pedidoRepository): Response
    {
//        $idUsuarioRegistrado = $session->get('id');
//        $usuarioRegistrado = $usuarioRepository->findOneById($idUsuarioRegistrado);
//        if ($usuarioRegistrado->getPuesto() == "administrador") {
//            $isAdmin=true;
//        }else{
//            $isAdmin=false;
//        }
        $usuariosRegistrados = $usuarioRepository->findAll();
        $proveedores = $proveedoresRepository->findAll();
        $pedidos = $pedidoRepository->findAll();
        $detallesPedido = $pedidoRepository->findAll();

        return $this->render('admin/admin.html.twig', [
            'accion' => $accion,
//            'isAdmin' => $isAdmin,
            'usuariosRegistrados' => $usuariosRegistrados,
            'proveedores' => $proveedores,
            'pedidos' => $pedidos,
            'detallesPedido' => $detallesPedido,
        ]);
    }
}