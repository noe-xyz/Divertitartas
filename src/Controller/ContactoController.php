<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactoController extends AbstractController
{
    #[Route('/contacto', name: 'contacto')]
    public function contacto(): Response
    {
        return $this->render('contacto/contacto.html.twig');
    }
}