<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\DetallesPedido;
use App\Entity\Pedido;
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
    public function pedidos(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $cliente = $session->get('id');
        $pedidosRealizadosPorClienteEnCurso = $entityManager->getRepository(Pedido::class)->findPedidoByEstadoAndCliente('en-proceso', $cliente, 'pendiente');
        $detallesPedido = [];
        foreach ($pedidosRealizadosPorClienteEnCurso as $pedido) {
            $detallesIdProd = [];
            foreach ($pedido->getDetalles() as $detalle) {
                $producto = $detalle->getProducto();
                if ($producto) {
                    $detallesIdProd[] = $producto;
                }
            }

            $detallesPedido[] = [
                'pedido' => $pedido,
                'detalles' => $detallesIdProd
            ];
        }

        $pedidosRealizadosPorClienteFinalizados = $entityManager->getRepository(Pedido::class)->findPedidoByEstadoAndCliente('listo', $cliente);
        $detallesPedidoFin = [];
        foreach ($pedidosRealizadosPorClienteFinalizados as $pedido) {
            $detallesIdProd = [];
            foreach ($pedido->getDetalles() as $detalle) {
                $producto = $detalle->getProducto();
                if ($producto) {
                    $detallesIdProd[] = $producto;
                }
            }

            $detallesPedidoFin[] = [
                'pedido' => $pedido,
                'detalles' => $detallesIdProd
            ];
        }

        return $this->render('pedidos/pedidos.html.twig', [
            'pedidosEnCurso' => $pedidosRealizadosPorClienteEnCurso,
            'pedidosFinalizados' => $pedidosRealizadosPorClienteFinalizados,
            'detallesPedido' => $detallesPedido,
            'detallesPedidoFin' => $detallesPedidoFin
        ]);
    }

    //TODO Algo con getIdPedido... para mostrarlo en la url y que redirija al pedido exacto
    #[Route('/factura/{id}', name: 'factura')]
    public function factura(int $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $pedido = $entityManager->getRepository(Pedido::class)->find($id);

        if (!$pedido) {
            throw $this->createNotFoundException('El pedido no existe');
        }


        $clienteId = $session->get('id');
        $cliente = $entityManager->getRepository(Cliente::class)->find($pedido->getIdCliente());
        if (!$cliente || $cliente->getId() !== $clienteId) {
            throw $this->createAccessDeniedException('No tienes permiso para ver esta factura.');
        }

        $detalles = $pedido->getDetalles();
        $productos = [];

        foreach ($detalles as $detalle) {
            $producto = $detalle->getProducto();
            if ($producto) {
                $productos[] = $producto;
            }
        }

        return $this->render('pedidos/factura.html.twig', [
            'pedido' => $pedido,
            'productos' => $productos,
            'cliente' => $cliente
        ]);
    }

    #[Route('/carrito', name: 'carrito')]
    public function carrito(EntityManagerInterface $entityManager, SessionInterface $session, Request $request): Response
    {
        $clienteRegistrado = $session->get('nombreCompleto');
        $cliente = null;
        $domicilioStr = null;

        if ($clienteRegistrado) {
            $cliente = $entityManager->getRepository(Cliente::class)->findOneBy(['nombreCompleto' => $clienteRegistrado]);

            if ($cliente && is_array($cliente->getDomicilio())) {
                $direccion = $cliente->getDomicilio();
                $orden = ['domicilio', 'piso', 'portal', 'puerta', 'cp', 'localidad', 'provincia'];
                $direccionOrdenada = [];

                foreach ($orden as $campo) {
                    if (!empty($direccion[$campo])) {
                        $direccionOrdenada[] = $direccion[$campo];
                    }
                }
                $domicilioStr = implode(', ', $direccionOrdenada);

            } elseif ($cliente) {
                $domicilioStr = $cliente->getDomicilio();
            }
        }

        $canjearPuntos = $request->query->getBoolean('canjearPuntos', false);
        $session->set('canjearPuntos', $canjearPuntos);

        $subtotal = $this->calcularPrecioTotal($session);
        $puntosDescontados = $this->canjearPuntos($cliente, $session, $subtotal);

        $session->set('descuento', $puntosDescontados['descuento']);

        $fechaDeEnvio = $this->calcularFechaEnvio();

        return $this->render('pedidos/carrito.html.twig', [
            'cliente' => $cliente,
            'fechaDeEnvio' => $fechaDeEnvio,
            'domicilioStr' => $domicilioStr,
            'subtotal' => $subtotal,
            'total' => $puntosDescontados['total'],
            'puntosCanjeados' => $canjearPuntos,
            'descuento' => $puntosDescontados['descuento'],
        ]);
    }

    public function canjearPuntos(?Cliente $cliente, SessionInterface $session, float $subtotal): array
    {
        $canjearPuntos = $session->get('canjearPuntos', false);
        $descuento = 0;

        if ($canjearPuntos && $cliente) {
            $puntos = $cliente->getPuntos();

            if ($puntos >= 10) {
                $bloques = intdiv($puntos, 10); // bloques de 10 pts para canjear
                $descuento = $bloques * 2;

                if ($descuento > $subtotal) {
                    $descuento = floor($subtotal);
                }

                $session->set('puntosCanjeadosCantidad', $bloques * 10);
            } else {
                $session->set('puntosCanjeadosCantidad', 0);
            }
        } else {
            $session->set('puntosCanjeadosCantidad', 0);
        }

        $subtotalConDescuento = max(0, $subtotal - $descuento);
        $total = $subtotalConDescuento + 5;

        return [
            'descuento' => $descuento,
            'subtotalConDescuento' => $subtotalConDescuento,
            'total' => $total
        ];
    }

    #[Route('/carrito/add', name: 'add-carrito', methods: ['POST'])]
    public function agregarAlCarrito(Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        if (!$session->has('nombreCompleto')) {
            return $this->redirectToRoute('login');
        } else {
            $productoId = $request->request->get('productoId');
            $sabor = $request->request->get('sabor');
            $relleno = $request->request->get('relleno');

            $productoSeleccionado = $entityManager->getRepository(Producto::class)->findOneBy(['id' => $productoId]);
            $categoria = $productoSeleccionado->getCategoria();
            $slug = $productoSeleccionado->getSlug();

            $carritoSesion = $session->get('carrito', []);
            $carritoSesion[] = [
                'id' => $productoSeleccionado->getId(),
                'nombre' => $productoSeleccionado->getNombre(),
                'precio' => $productoSeleccionado->getPrecio(),
                'sabor' => $sabor,     //$productoSeleccionado->getSabor(),
                'relleno' => $relleno,
                'imagen' => $productoSeleccionado->getImagen(),
            ];

            $session->set('carrito', $carritoSesion);
        }
        return $this->redirectToRoute('producto', [
            'categoria' => $categoria,
            'slug' => $slug,]);
    }

    #[Route('/carrito/eliminar/{id}', name: 'eliminar-carrito', methods: ['POST'])]
    public function eliminarDelCarrito(int $id, Request $request, SessionInterface $session): Response
    {
        $carrito = $request->getSession()->get('carrito', []);

        $nuevoCarrito = array_filter($carrito, function ($producto) use ($id) {
            return $producto['id'] != $id;
        });

        if (empty($nuevoCarrito)) {
            $session->remove('carrito');
        } else {
            $session->set('carrito', $nuevoCarrito);
        }

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
        $subtotal = $this->calcularPrecioTotal($session);
        $descuento = $session->get('puntosCanjeadosCantidad', 0) / 10 * 2;

        $subtotalConDescuento = max(0, $subtotal - $descuento);
        $envio = 5;

        return $subtotalConDescuento + $envio;
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

    #[Route('/pago/confirmacion', name: 'confirmacion', methods: ['POST'])]
    public function confirmacion(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $carritoSesion = $session->get('carrito', []);
        $clienteSesion = $session->get('id');
        $cliente = $entityManager->getRepository(Cliente::class)->findOneBy(['id' => $clienteSesion]);

        $subtotal = $this->calcularPrecioTotal($session);
        $descuento = $session->get('descuento', 0);
        $total = max(0, $subtotal - $descuento) + 5;

        $pedido = new Pedido();
        $pedido->setFecha(new \DateTime())
            ->setEstado('pendiente')
            ->setCosteTotal($total)
            ->setIdCliente($clienteSesion);

        $puntosCanjeados = $session->get('puntosCanjeadosCantidad', 0);
        $puntosPrevios = $cliente->getPuntos();
        $puntosActualizados = max(0, $puntosPrevios - $puntosCanjeados);

        $puntosGanados = count($carritoSesion) * 5; // Podrías usar cantidad real
        $cliente->setPuntos($puntosActualizados + $puntosGanados);

        foreach ($carritoSesion as $carrito) {
            $producto = $entityManager->getRepository(Producto::class)->find($carrito['id']);
            if (!$producto) {
                continue;
            }

            $detallesPedido = new DetallesPedido();
            $detallesPedido->setPedido($pedido)
                ->setProducto($producto)
                ->setPrecioUnitario($carrito['precio'])
                ->setCantidad(1); // Cambiar para manejar cantidad real input

            $pedido->addDetalle($detallesPedido);
            $entityManager->persist($detallesPedido);
        }

        $entityManager->persist($cliente);
        $entityManager->persist($pedido);
        $entityManager->flush();

        $session->set('puntos', $cliente->getPuntos());
        $session->remove('carrito');
        $session->remove('puntosCanjeadosCantidad');
        $session->remove('canjearPuntos');
        $session->remove('descuento');

        return $this->render('pedidos/confirmacion.html.twig', [
            'carrito' => $carritoSesion,
            'total' => $total,
            'descuento' => $descuento,
            'subtotal' => $subtotal,
            'puntosGanados' => $puntosGanados,
        ]);
    }
}