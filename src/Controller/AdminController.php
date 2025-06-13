<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\DetallesPedido;
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Entity\Proveedores;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/administracion/{accion}', name: 'mostrar-accion', requirements: ['accion' => 'comunicados|usuarios|proveedores|contabilidad|pedidos|stock|configuracion'])]
    public function mostrarAccion(string $accion, Session $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        $idUsuarioRegistrado = $session->get('id');
        $usuarioRegistrado = $entityManager->getRepository(Usuario::class)->findOneBy(['id' => $idUsuarioRegistrado]);

        if (get_class($usuarioRegistrado) !== Trabajador::class) {
            return $this->redirectToRoute('index');
        }

        //Pestaña gestión usuarios
        $clientesRegistrados = $entityManager->getRepository(Cliente::class)->findAll();
        $trabajadoresRegistrados = $entityManager->getRepository(Trabajador::class)->findAll();

        $nombre = $request->query->get('nombre');
        $correo = $request->query->get('correo');

        $tabActiva = $request->query->get('tab', 'clientes');

        if ($tabActiva === 'clientes') {
            $clientesRegistrados = $entityManager
                ->getRepository(Cliente::class)
                ->findClienteByNombreYCorreo($nombre, $correo);
        } else {
            $trabajadoresRegistrados = $entityManager
                ->getRepository(Trabajador::class)
                ->findTrabajadorByNombreYCorreo($nombre, $correo);
        }


        //Pestaña proveedores
        $proveedores = $entityManager->getRepository(Proveedores::class)->findAll();

        //Pestaña contabilidad
        $fechaInicio = $request->query->get('fechaInicio');
        $fechaFin = $request->query->get('fechaFin');

        $pedidosContabilidad = $entityManager->getRepository(Pedido::class)->findPedidoByFechas($fechaInicio, $fechaFin);
        $pedidosYClientes = [];
        foreach ($pedidosContabilidad as $pedido) {
            $clienteId = $pedido->getIdCliente();
            $cliente = $entityManager->getRepository(Cliente::class)->findOneById($clienteId);

            $pedidosYClientes[] = [
                'pedido' => $pedido,
                'fecha' => $pedido->getFecha(),
                'clienteNombre' => $cliente,
            ];

        }

        $totalVentas = $this->calcularTotalVentas($pedidosContabilidad);

        //Pestaña pedidos
        $pedidosMostrar = $entityManager->getRepository(Pedido::class)->findAll();
        $detallesPedido = $entityManager->getRepository(DetallesPedido::class)->findAll();

        $pedidosConFechaNombre = [];
        foreach ($pedidosMostrar as $pedido) {
            $productoNames = [];

            foreach ($pedido->getDetalles() as $detalle) {
                $idProd = $detalle->getIdProducto();
                $nombre = $entityManager->getRepository(Producto::class)->findNameById($idProd);
                $productoNames[] = $nombre ?? 'Nombre no encontrado';
            }

            $fechaEnvio = $this->calcularFechaEnvio($pedido);

            $pedidosConFechaNombre[] = [
                'pedido' => $pedido,
                'fechaPedido' => $pedido->getFecha(),
                'fechaEnvio' => $this->calcularFechaEnvio($pedido),
                //'nombre' => $pedido->getNombre(),
                'productoNames' => $productoNames,
            ];
        }

        $pedidosAModificarEstado = $request->query->all();
        foreach ($pedidosAModificarEstado as $key => $value) {
            if (str_starts_with($key, 'estado_')) {
                $pedidoId = substr($key, strlen('estado_'));
                $pedido = $entityManager->getRepository(Pedido::class)->find($pedidoId);
                if ($pedido) {
                    // Asumiendo que el setter es setEstado()
                    $pedido->setEstado($value);
                    $entityManager->persist($pedido);
                }
            }
        }
        $entityManager->flush();

        //Pestaña stock
        $productos = $entityManager->getRepository(Producto::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'accion' => $accion,
            'clientesRegistrados' => $clientesRegistrados,
            'trabajadoresRegistrados' => $trabajadoresRegistrados,
            'tabActiva' => $tabActiva,
            'proveedores' => $proveedores,
            'pedidosContabilidad' => $pedidosContabilidad,
            'pedidosMostrar' => $pedidosMostrar,
            'pedidosYClientes' => $pedidosYClientes,
            'totalVentas' => $totalVentas,
            'detallesPedido' => $detallesPedido,
            'pedidosConFechaNombre' => $pedidosConFechaNombre,
            'productos' => $productos,
        ]);
    }

    private function calcularTotalVentas($pedidos): float
    {
        $totalVentas = 0;
        foreach ($pedidos as $pedido) {
            $totalVentas += $pedido->getCosteTotal();
        }

        return $totalVentas;
    }

    public function calcularFechaEnvio($pedido): \DateTime
    {
        $fechaPedido = clone $pedido->getFecha();
        $fechaPedido->modify('+5 days');

        return $fechaPedido;
    }
}