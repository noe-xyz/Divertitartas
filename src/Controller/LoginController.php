<?php

namespace App\Controller;

use App\Repository\ClienteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{

    #[Route('/login', name: 'login')]
    public function login(ClienteRepository $clienteRepository, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (isset($_POST['submit'])) {
                $email = $request->request->get('email');
                $password = $request->request->get('password');

                $usuarioLogueado=$clienteRepository->findLoggedClient($email, $password);
                if (!$usuarioLogueado) {
                    return $this->redirectToRoute('registro');
                    #TODO añadir algo que avise al usuario de que debe registrarse (necesito hacer redirect?)
                }
            }
        }
        return $this->render('login/login.html.twig');
    }
}