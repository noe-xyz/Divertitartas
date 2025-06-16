<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Descuento;
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

        $direccionStr = $this->separarDireccion($cliente);

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
            'cliente' => $cliente,
            'direccionStr' => $direccionStr
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
                $this->separarDireccion($cliente);
            } elseif ($cliente) {
                $domicilioStr = $cliente->getDomicilio();
            }
        }

        $canjearPuntos = $request->query->getBoolean('canjearPuntos', false);
        $codigoDescuento = trim($request->query->get('codigoDescuento', ''));

        $session->set('canjearPuntos', $canjearPuntos);

        $subtotal = $this->calcularPrecioTotal($session);

        $porcentaje = 0;
        $usarCupon = false;

        if ($codigoDescuento !== '') {
            $codigo = $entityManager->getRepository(Descuento::class)->findOneBy(['codigo_descuento' => $codigoDescuento]);
            if ($codigo) {
                $porcentaje = $codigo->getCantidadDescontada();
                $descuentoCuponCantidad = $subtotal * $porcentaje / 100;
                $usarCupon = true;
            } else {
                // Código no válido, limpiar parámetro para que no quede activo
                $codigoDescuento = '';
            }
        } else {
            $porcentaje = 0;
            $descuentoCuponCantidad = 0;
        }

        $puntosDescontados = $this->canjearPuntos($cliente, $session, $subtotal);

        $total = max(0, $subtotal - $descuentoCuponCantidad - $puntosDescontados['descuento']);

        $session->set('descuentoCupon', $descuentoCuponCantidad);
        $session->set('descuentoPuntos', $puntosDescontados['descuento']);
        $session->set('descuentoCuponPorcentaje', $porcentaje);
        $session->set('usarCupon', $usarCupon);


        $fechaDeEnvio = $this->calcularFechaEnvio();

        return $this->render('pedidos/carrito.html.twig', [
            'cliente' => $cliente,
            'fechaDeEnvio' => $fechaDeEnvio,
            'domicilioStr' => $domicilioStr,
            'subtotal' => $subtotal,
            'total' => $total,
            'puntosCanjeados' => $canjearPuntos,
            'descuento' => $puntosDescontados['descuento'],
            'usarCupon' => $usarCupon,
            'codigoDescuento' => $codigoDescuento,
            'descuentoCuponPorcentaje' => $porcentaje,
            'descuentoCuponAmount' => $descuentoCuponCantidad,
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
            $cantidad = max((int)$request->request->get('cantidad', 1), 1);
            $mensaje = $request->request->get('mensajePersonalizado');

            $productoSeleccionado = $entityManager->getRepository(Producto::class)->findOneBy(['id' => $productoId]);
            $categoria = $productoSeleccionado->getCategoria();
            $slug = $productoSeleccionado->getSlug();

            $carrito = $session->get('carrito', []);
            $productoYaExiste = false;

            foreach ($carrito as &$item) {
                if ($item['id'] === $productoSeleccionado->getId()) {
                    $item['cantidad'] = ($item['cantidad'] ?? 1) + $cantidad;
                    $productoYaExiste = true;
                    break;
                }
            }

            if (!$productoYaExiste) {
                $carrito[] = [
                    'id' => $productoSeleccionado->getId(),
                    'nombre' => $productoSeleccionado->getNombre(),
                    'precio' => $productoSeleccionado->getPrecio(),
                    'sabor' => $sabor,
                    'relleno' => $relleno,
                    'imagen' => $productoSeleccionado->getImagen(),
                    'cantidad' => $cantidad,
                    'mensaje' => $mensaje
                ];
            }

            $session->set('carrito', $carrito);
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

    #[Route('/carrito/actualizar/{index}', name: 'actualizar-carrito', methods: ['POST'])]
    public function actualizarCantidad(int $index, Request $request, SessionInterface $session): Response
    {
        $nuevaCantidad = max((int)$request->request->get('cantidad'), 1);
        $carrito = $session->get('carrito', []);

        if (isset($carrito[$index])) {
            $carrito[$index]['cantidad'] = $nuevaCantidad;
            $session->set('carrito', $carrito);
        }

        return $this->redirectToRoute('carrito');
    }

    public function calcularPrecioTotal(SessionInterface $session): float
    {
        $carritoSesion = $session->get('carrito', []);
        $precioTotal = 0;
        foreach ($carritoSesion as $carrito) {
            $cantidad = $carrito['cantidad'] ?? 1;
            $precioTotal += $carrito['precio'] * $cantidad;
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
    public function confirmacion(SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        // 1. Datos básicos
        $carritoSesion = $session->get('carrito', []);
        $clienteSesion = $session->get('id');
        $cliente = $entityManager->getRepository(Cliente::class)->find($clienteSesion);

        // 2. Subtotal sin descuentos
        $subtotal = $this->calcularPrecioTotal($session);

        // 3. Recuperar descuentos de la sesión
        $descuentoPuntos = $session->get('descuentoPuntos', 0);
        $descuentoCupon = $session->get('descuentoCupon', 0);

        // 4. Calcular total final
        $descuentoTotal = $descuentoPuntos + $descuentoCupon;
        $total = max(0, $subtotal - $descuentoTotal) + 5; // + envío

        // 5. Crear Pedido
        $pedido = new Pedido();
        $pedido->setFecha(new \DateTime())
            ->setEstado('pendiente')
            ->setCosteTotal($total)
            ->setIdCliente($clienteSesion);

        // 6. Ajustar puntos: restar canjeados, sumar ganados
        $puntosCanjeados = $session->get('puntosCanjeadosCantidad', 0);
        $puntosPrevios = $cliente->getPuntos();
        $puntosActuales = max(0, $puntosPrevios - $puntosCanjeados);

        $puntosGanados = 0;
        foreach ($carritoSesion as $item) {
            $puntosGanados += $item['cantidad'] * 5;
        }
        $cliente->setPuntos($puntosActuales + $puntosGanados);

        // 7. Persistir DetallesPedido
        foreach ($carritoSesion as $item) {
            $producto = $entityManager->getRepository(Producto::class)->find($item['id']);
            if (!$producto) {
                continue;
            }
            $detalle = new DetallesPedido();
            $detalle->setPedido($pedido)
                ->setProducto($producto)
                ->setPrecioUnitario($item['precio'])
                ->setCantidad($item['cantidad']);
            $pedido->addDetalle($detalle);
            $entityManager->persist($detalle);
        }

        $entityManager->persist($cliente);
        $entityManager->persist($pedido);
        $entityManager->flush();

        // 8. Limpiar sesión
        $session->set('puntos', $cliente->getPuntos());
        $session->remove('carrito');
        $session->remove('puntosCanjeadosCantidad');
        $session->remove('descuentoPuntos');
        $session->remove('descuentoCupon');
        $session->remove('canjearPuntos');

        // 9. Renderizar confirmación con todos los datos
        return $this->render('pedidos/confirmacion.html.twig', [
            'carrito' => $carritoSesion,
            'subtotal' => $subtotal,
            'descuentoPuntos' => $descuentoPuntos,
            'descuentoCupon' => $descuentoCupon,
            'descuentoTotal' => $descuentoTotal,
            'total' => $total,
            'puntosGanados' => $puntosGanados,
            'pago' => $request->request->get('payment_method'),
        ]);
    }


    public function separarDireccion($cliente): string
    {
        $direccion = $cliente->getDomicilio();
        $orden = ['domicilio', 'piso', 'portal', 'puerta', 'cp', 'localidad', 'provincia'];
        $direccionOrdenada = [];

        foreach ($orden as $campo) {
            if (!empty($direccion[$campo])) {
                $direccionOrdenada[] = $direccion[$campo];
            }
        }
        $domicilioStr = implode(', ', $direccionOrdenada);

        return $domicilioStr;
    }
}