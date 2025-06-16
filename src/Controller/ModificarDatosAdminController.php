<?php

namespace App\Controller;

use App\Entity\Descuento;
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Entity\Proveedores;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ModificarDatosAdminController extends AbstractController
{
    #[Route('/administracion/eliminar-usuario/{id}', name: 'eliminar-usuario', methods: ['POST'])]
    public function eliminarCuenta(int $id, SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        $usuario = $entityManager->getRepository(Usuario::class)->find($id);

        $usuario->setEliminado(true);
        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'usuarios'
        ]);
    }


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

    #[Route('/administracion/add-codigo-descuento', name: 'add-codigo-descuento')]
    public function addCodigo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cantidadDescontada = $request->request->get('descuento') ?? 0;

        $descuento = new Descuento();
        $descuento->setCodigoDescuento('diver-' . $cantidadDescontada)
            ->setNombre('diver-' . $cantidadDescontada . '%')
            ->setCantidadDescontada($cantidadDescontada);
        $entityManager->persist($descuento);

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

    #[Route('/administracion/crear-producto', name: 'crear-producto')]
    public function crearProducto(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $producto = new Producto();

        $slug = $this->crearSlugUrl($request->request->get('nombre'));
        $slugCategoria = $this->crearSlugUrl($request->request->get('categoria'));

        $producto->setNombre($request->request->get('nombre'))
            ->setPrecio((float)$request->request->get('precio'))
            ->setSabor($request->request->get('sabor') ?? null)
            ->setRelleno($request->request->get('relleno') ?? null)
            ->setRaciones($request->request->get('raciones'))
            ->setDescripcion($request->request->get('descripcion'))
            ->setComentario($request->request->get('comentario'))
            ->setCategoria($slugCategoria)
            ->setSlug($slug)
            ->setCantidad((int)$request->request->get('cantidad'));

        $imagen = $request->files->get('imagen');
        if ($imagen) {
            $nombreSeguro = $slugger->slug(pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME));
            $nuevoNombre = $nombreSeguro . '-' . uniqid() . '.' . $imagen->guessExtension();
            $imagen->move($this->getParameter('productos_directory'), $nuevoNombre);
            $producto->setImagen($nuevoNombre);
        }

        $entityManager->persist($producto);
        $entityManager->flush();

        return $this->redirectToRoute('mostrar-accion', [
            'accion' => 'stock',
        ]);
    }

    private function crearSlugUrl($nombreProducto)
    {
        // Convertir a minúsculas
        $slug = strtolower($nombreProducto);
        // Eliminar acentos y caracteres especiales
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        // Reemplazar cualquier carácter que no sea letra o número por un espacio
        $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);
        // Reemplazar múltiples espacios por un solo guion
        $slug = preg_replace('/\s+/', '-', trim($slug));

        return $slug;
    }

}