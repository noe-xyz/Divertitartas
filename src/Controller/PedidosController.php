<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Producto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class PedidosController extends AbstractController
{
    #[Route('/pedidos', name: 'pedido')]
    public function pedidos(): Response
    {
        return $this->render('pedidos/pedidos.html.twig');
    }

    //TODO Algo con getIdPedido... para mostrarlo en la url y que redirija al pedido exacto
    #[Route('/factura', name: 'factura')]
    public function factura(): Response
    {
        return $this->render('pedidos/factura.html.twig');
    }

    #[Route('/carrito', name: 'carrito')]
    public function carrito(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $clienteRegistrado = $session->get('nombreCompleto');
        $cliente = $entityManager->getRepository(Cliente::class)->findOneBy(['nombreCompleto' => $clienteRegistrado]);
        $domicilioStr = implode(', ', $cliente->getDomicilio());
        $fechaDeEnvio = $this->calcularFechaEnvio();
        $subtotal = $this->calcularPrecioTotal($session);
        $total = $this->calcularPrecioConEnvio($session);

        return $this->render('pedidos/carrito.html.twig', [
            'cliente' => $cliente,
            'fechaDeEnvio' => $fechaDeEnvio,
            'domicilioStr' => $domicilioStr,
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }

    #[Route('/carrito/add', name: 'add-carrito', methods: ['POST'])]
    public function agregarAlCarrito(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $productoId = $request->request->get('productoId');
        $sabor = $request->request->get('sabor');
        $relleno = $request->request->get('relleno');

        $productoSeleccionado = $entityManager->getRepository(Producto::class)->findOneBy(['id' => $productoId]);

        $carritoSesion = $session->get('carrito', []);
        $carritoSesion[] = [
            'id' => $productoSeleccionado->getId(),
            'nombre' => $productoSeleccionado->getNombre(),
            'precio' => $productoSeleccionado->getPrecio(),
            'sabor' => $sabor,     //$productoSeleccionado->getSabor(),
            'relleno' => $relleno,
        ];

        $session->set('carrito', $carritoSesion);

        return $this->redirectToRoute('carrito');
    }

    #[Route('/carrito/eliminar/{id}', name: 'eliminar-carrito', methods: ['POST'])]
    public function eliminarDelCarrito(int $id, Request $request): Response
    {
        $carrito = $request->getSession()->get('carrito', []);

        $nuevoCarrito = array_filter($carrito, function ($producto) use ($id) {
            return $producto['id'] != $id;
        });

        $request->getSession()->set('carrito', $nuevoCarrito);

        return $this->redirectToRoute('carrito');
    }


    public function calcularPrecioTotal(SessionInterface $session): float
    {
        $carritoSesion = $session->get('carrito', []);
        $precioTotal = 0;
        foreach ($carritoSesion as $carrito) {
            $precioTotal += $carrito['precio']; //* $carrito['cantidad'];
        }

        return $precioTotal;
    }

    public function calcularPrecioConEnvio(SessionInterface $session): float
    {
        $precioTotal = $this->calcularPrecioTotal($session) + 5;

        return $precioTotal;
    }

    public function calcularFechaEnvio(): \DateTime
    {
        $fechaEnvio = new \DateTime();
        $fechaEnvio->modify('+5 days');

        return $fechaEnvio;
    }

    #[Route('/pago', name: 'pago')]
    public function pago(): Response
    {
        return $this->render('pedidos/pago.html.twig');
    }

    #[Route('/pago/cvv', name: 'cvv')]
    public function CVV(): Response
    {
        return $this->render('pedidos/cvv.html.twig');
    }

    #[Route('/pago/confirmacion', name: 'confirmacion')]
    public function confirmacion(SessionInterface $session): Response
    {
        $carritoSesion = $session->get('carrito', []);
        $total = $this->calcularPrecioConEnvio($session);

        return $this->render('pedidos/confirmacion.html.twig', [
            'carrito' => $carritoSesion,
            'total' => $total,
        ]);
    }
}