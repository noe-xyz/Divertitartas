<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function carrito(): Response
    {
        return $this->render('pedidos/carrito.html.twig');
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
    public function confirmacion(): Response
    {
        return $this->render('pedidos/confirmacion.html.twig');
    }
}