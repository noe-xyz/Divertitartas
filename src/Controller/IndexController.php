<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session): Response
    {
        $usuarioRegistrado = $session->get('nombreCompleto');
        $id = $session->get('id');

        return $this->render('index/index.html.twig', [
            'usuarioRegistrado' => $usuarioRegistrado,
            'id' => $id,
        ]);
    }
}