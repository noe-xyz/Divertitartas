<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    #[Route('/privacidad', name: 'legal-privacidad')]
    public function privacidad(): Response
    {
        return $this->render('legal/privacidad.html.twig');
    }

    #[Route('/aviso-legal', name: 'legal-aviso')]
    public function avisoLegal(): Response
    {
        return $this->render('legal/avisoLegal.html.twig');
    }

    #[Route('/cookies', name: 'legal-cookies')]
    public function cookies(): Response
    {
        return $this->render('legal/cookies.html.twig');
    }

    #[Route('/venta-cancelacion', name: 'legal-venta')]
    public function venta(): Response
    {
        return $this->render('legal/ventaCancelacion.html.twig');
    }

}