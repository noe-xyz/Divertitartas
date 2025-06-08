<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiciosController extends AbstractController
{
    #[Route('/servicios', name: 'servicios')]
    public function servicios(): Response
    {
        return $this->render('servicios/servicios.html.twig');
    }
}