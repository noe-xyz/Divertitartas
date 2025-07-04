<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #TODO dividir en funciones para no hacer un pedazo de bloque de código
    #Ruta de la página web
    #[Route('/login', name: 'login')]
    public function login(UsuarioRepository $usuarioRepository, Request $request, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST') && isset($_POST['submit'])) {
            #Recoger en variables los datos introducidos en el formulario
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            #Query que busca en la base de datos si existe el usuario y lógica por si no lo está (no está registrado)
            $usuarioExiste = $usuarioRepository->findRegisteredUser($email, $password);
            if ($usuarioExiste) {
                if ($usuarioExiste && $usuarioExiste->isEliminado()) {
                    return $this->render('login/login.html.twig', [
                        "error" => true,
                        "titulo" => "Cuenta eliminada",
                        "mensaje" => 'Tu cuenta ha sido eliminada. Contacta con nosotros si crees que esto es un error.',
                        "boton" => false,
                        "mensajeBtn" => "",
                        "enlaceBtn" => ""
                    ]);
                }

                #Crear la sesión del usuario
                $this->crearSesion($session, $usuarioExiste, $entityManager);
                #Redirigir al index
                if (get_class($usuarioExiste) === Trabajador::class) {
                    return $this->redirectToRoute('mostrar-accion', ['accion' => 'comunicados']);
                }
                return $this->redirectToRoute('index');
            } else {
                return $this->render('login/login.html.twig', [
                    "error" => true,
                    "titulo" => "Error",
                    "mensaje" => 'Las credenciales no existen en nuestra base de datos.',
                    "boton" => true,
                    "mensajeBtn" => "¿Quieres registrarte?",
                    "enlaceBtn" => "registro"
                ]);
            }
        }
        #Cualquier función que maneje la lógica principal de una vista debe devolver una plantilla twig (lo que se ve en la vista)
        return $this->render('login/login.html.twig', [
            "error" => false,
            "titulo" => "",
            "mensaje" => "",
            "boton" => false,
            "mensajeBtn" => "",
            "enlaceBtn" => ""
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request, SessionInterface $session): Response
    {
        print_r($request->request->all());
        if ($request->isMethod('POST') && $request->request->has('logout')) {
            $session->invalidate();
            print_r($request->request->all());
            return $this->redirectToRoute('index');
        }
        return $this->redirectToRoute('index');
    }

    public function crearSesion(SessionInterface $session, $usuario): void
    {
        $session->set('id', $usuario->getId());
        $session->set('email', $usuario->getEmail());
        $session->set('nombreCompleto', $usuario->getNombreCompleto());
        if (get_class($usuario) === Cliente::class) {
            $session->set('nombre', $usuario->getNombre());
            $session->set('puntos', $usuario->getPuntos());
        } elseif (get_class($usuario) === Trabajador::class) {
            $session->set('puesto', $usuario->getPuesto());
        }
    }
}