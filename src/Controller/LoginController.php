<?php

namespace App\Controller;

use App\Repository\ClienteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #TODO dividir en funciones para no hacer un pedazo de bloque de código
    #Ruta de la página web
    #[Route('/login', name: 'login')]
    public function login(ClienteRepository $clienteRepository, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if (isset($_POST['submit'])) {
                #Recoger en variables los datos introducidos en el formulario
                $email = $request->request->get('email');
                $password = $request->request->get('password');

                #Query que busca en la base de datos si existe el usuario y lógica por si no lo está (no está registrado)
                $usuarioExiste=$clienteRepository->findRegisteredClient($email, $password);
                if (!$usuarioExiste) {
                    $this->addFlash('error', 'La contraseña o el usuario no existe en nuestra base de datos.');
                    return $this->redirectToRoute('registro');
                    #TODO poner algo que avise al usuario de que debe registrarse/revisar lo que ha escrito (necesito hacer redirect? flash?)
                }
                #TODO avisar de haberse logueado correctamente
            }
        }
        #Cualquier función que maneje la lógica principal de una vista debe devolver una plantilla twig (lo que se ve en la vista)
        return $this->render('login/login.html.twig');
    }
}