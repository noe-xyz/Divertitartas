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
        $pedidosRealizadosPorClienteEnCurso = $entityManager->getRepository(Pedido::class)->findPedidoByEstadoAndCliente('en-proceso', $cliente);
        $detallesPedido = [];
        foreach ($pedidosRealizadosPorClienteEnCurso as $pedido) {
            $detallesIdProd = [];
            foreach ($pedido->getDetalles() as $detalle) {
                $detallesIdProd[] = $detalle->getIdProducto();
            }

            $detallesPedido[] = [
                'pedido' => $pedido,
                'detalles' => $detallesIdProd
            ];
        }

        return $this->render('pedidos/pedidos.html.twig', [
            'pedidosEnCurso' => $pedidosRealizadosPorClienteEnCurso,
            'detallesPedido' => $detallesPedido
        ]);
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
        if (is_array($cliente->getDomicilio())) {
            $direccion = $cliente->getDomicilio();
            $orden = ['domicilio', 'piso', 'portal', 'puerta', 'cp', 'localidad', 'provincia'];
            $direccionOrdenada = [];

            foreach ($orden as $campo) {
                if (!empty($direccion[$campo])) {
                    $direccionOrdenada[] = $direccion[$campo];
                }
            }

            $domicilioStr = implode(', ', $direccionOrdenada);
        } else {
            $domicilioStr = $cliente->getDomicilio();
        }
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
            ];

            $session->set('carrito', $carritoSesion);
        }
        return $this->redirectToRoute('producto', [
            'categoria' => $categoria,
            'slug' => $slug,]);
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

    #[Route('/pago/confirmacion', name: 'confirmacion', methods: ['POST'])]
    public function confirmacion(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $carritoSesion = $session->get('carrito', []);
        $clienteSesion = $session->get('id');
        //$cliente = $entityManager->getRepository(Cliente::class)->findOneBy(['id' => $clienteSesion]);
        $total = $this->calcularPrecioConEnvio($session);

        $pedido = new Pedido();
        $pedido->setFecha(new \DateTime())
            ->setEstado('en-proceso')
            ->setCosteTotal($total)
            ->setIdCliente($clienteSesion);

        foreach ($carritoSesion as $carrito) {
            $detallesPedido = new DetallesPedido();
            $detallesPedido->setPedido($pedido)
                ->setIdProducto($carrito['id'])
                ->setPrecioUnitario($carrito['precio'])
                ->setCantidad(1); //cambiar por el input numérico de carrito

            $pedido->addDetalle($detallesPedido);

            $entityManager->persist($detallesPedido);
        }

        $entityManager->persist($pedido);
        $entityManager->flush();

        $session->remove('carrito');


        return $this->render('pedidos/confirmacion.html.twig', [
            'carrito' => $carritoSesion,
            'total' => $total,
        ]);
    }
}