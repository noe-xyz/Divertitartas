<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Repository\ClienteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{

    #[Route('/login')]
    public function login(ClienteRepository $clienteRepository): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['submit'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];

                $usuarioLogueado=$clienteRepository->findLoggedClient($email, $password);
                if (!$usuarioLogueado) {
                    $this->redirectToRoute('registro');
                }
            }
        }
        return $this->render('login/login.html.twig');
    }
}