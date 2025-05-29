<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QuienesSomosController extends AbstractController
{
    #[Route('/quienes-somos', name: 'quienes-somos')]
    public function quienesSomos(): Response
    {
        return $this->render('quienesSomos/quienesSomos.html.twig');
    }
}