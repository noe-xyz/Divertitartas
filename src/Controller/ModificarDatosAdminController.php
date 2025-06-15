<?php

namespace App\Controller;

use App\Entity\Pedido;
use App\Entity\Producto;
use App\Entity\Proveedores;
use App\Entity\Trabajador;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ModificarDatosAdminController extends AbstractController
{
    #[Route('/administracion/add-trabajador', name: 'add-trabajador')]
    public function addTrabajador(Request $request, EntityManagerInterface $entityManager): Response
    {
        #Recoger en variables los datos introducidos en el formulario
        $campos = [
            'nombreCompleto', 'email', 'telefono', 'turno', 'puesto', 'password'
        ];

        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = $request->request->get($campo);
        }
        $nuevoTrabajador = new Trabajador();
        $nuevoTrabajador->setNombreCompleto($datos['nombreCompleto'])
            ->setEmail($datos['email'])
            ->setTelefono1($datos['telefono'])
            ->setTurno($datos['turno'])
            ->setPuesto($datos['puesto'])
            ->setPassword($datos['password']);

        $entityManager->persist($nuevoTrabajador);
        $entityManager->flush();

        $tab = $request->query->get('tab');

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'usuarios',
            'tab' => $tab]);
    }

    #[Route('/administracion/add-proveedor', name: 'add-proveedor')]
    public function addProveedor(Request $request, EntityManagerInterface $entityManager): Response
    {
        #Recoger en variables los datos introducidos en el formulario
        $campos = [
            'nombre', 'telefono', 'correo', 'direccion', 'notas'
        ];

        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = $request->request->get($campo) ?? null;
        }
        $nuevoProveedor = new Proveedores();
        $nuevoProveedor->setNombre($datos['nombre'])
            ->setTelefono($datos['telefono'])
            ->setCorreo($datos['correo'])
            ->setDireccion($datos['direccion'])
            ->setNotas($datos['notas']);

        $entityManager->persist($nuevoProveedor);
        $entityManager->flush();

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'proveedores']);
    }

//    #[Route('/administracion/add-producto', name: 'add-producto')]
//    public function addProducto(Request $request, EntityManagerInterface $entityManager): Response
//    {
//
//    }

    #[Route('/administracion/cambiar-estado', name: 'cambiar-estado')]
    public function cambiarEstado(Request $request, EntityManagerInterface $entityManager)
    {
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

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'pedidos',
        ]);

    }

    #[Route('/administracion/add-descuento', name: 'add-descuento')]
    public function addDescuento(Request $request, EntityManagerInterface $entityManager): Response
    {
        $descuento = $request->request->get('descuentoGeneral') ?? 0;

        $productos = $entityManager->getRepository(Producto::class)->findAll();
        foreach ($productos as $producto) {
            $precioConDescuento = round(($producto->getPrecio()) * (1 - ($descuento * 0.01)), 2);
            $producto->setPrecio($precioConDescuento);
            $entityManager->persist($producto);
        }

        $entityManager->flush();

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'stock',
        ]);
    }

    #[Route('/administracion/modificar-stock', name: 'modificar-stock')]
    public function modificarStock(Request $request, EntityManagerInterface $entityManager): Response
    {
        $allRequest = $request->request->all();
        $cantidades = isset($allRequest['cantidad']) && is_array($allRequest['cantidad']) ? $allRequest['cantidad'] : [];

        foreach ($cantidades as $productoId => $nuevaCantidad) {
            $producto = $entityManager->getRepository(Producto::class)->find($productoId);
            if ($producto && is_numeric($nuevaCantidad)) {
                $producto->setCantidad((int)$nuevaCantidad);
                $entityManager->persist($producto);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'stock',
        ]);
    }

    #[Route('/administracion/add-impuesto', name: 'add-impuesto')]
    public function addImpuesto(Request $request, EntityManagerInterface $entityManager): Response
    {
        $iva = $request->request->get('iva') ?? 21;

        $productos = $entityManager->getRepository(Producto::class)->findAll();
        foreach ($productos as $producto) {
            $nuevoPrecio = round(($producto->getPrecio()) * (1 + ($iva * 0.01)), 2);
            $producto->setPrecio($nuevoPrecio);
            $entityManager->persist($producto);
        }
        $entityManager->flush();

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'configuracion',
        ]);
    }

}