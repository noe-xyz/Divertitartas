<?php

namespace App\Controller;

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
            $usuarioExiste = $usuarioRepository->findOneBy(['email' => $email]);
            if ($usuarioExiste) {
                if (password_verify($password, $usuarioExiste->getPassword())) {
                    #TODO avisar de haberse logueado correctamente
                    #Crear la sesión del usuario
                    $this->crearSesion($session, $usuarioExiste, $entityManager);
                    #Redirigir al index
                    $ruta = $this->conseguirTipoUsuario($entityManager, $usuarioExiste) === "trabajador" ? 'admin' : 'index';
                    return $this->redirectToRoute($ruta);
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
        if ($request->isMethod('POST') && $request->request->has('logout')) {
            $session->invalidate();
            return $this->redirectToRoute('index');
        }
        return $this->redirectToRoute('index');
    }

    public function crearSesion(SessionInterface $session, Usuario $usuario, EntityManagerInterface $entityManager): void
    {
        $session->set('id', $usuario->getId());
        $session->set('email', $usuario->getEmail());
        $session->set('nombreCompleto', $usuario->getNombreCompleto());
        if ($this->conseguirTipoUsuario($entityManager, $usuario) === "cliente") {
            $session->set('puntos', $usuario->getPuntos());
        }
    }

    public function conseguirTipoUsuario(EntityManagerInterface $entityManager, Usuario $usuario): ?string
    {
        $classMetadata = $entityManager->getClassMetadata(Usuario::class);
        $discriminatorMap = $classMetadata->discriminatorMap;
        return array_search(get_class($usuario), $discriminatorMap, true);
    }
}