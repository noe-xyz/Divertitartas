<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Empresa;
use App\Repository\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistroController extends AbstractController
{
    #TODO dividir en funciones para no hacer un pedazo de bloque de código
    #Ruta de la página web
    #[Route('/registro', name: 'registro')]
    public function registro(EntityManagerInterface $entityManager, Request $request, ClienteRepository $clienteRepository): Response
    {
        #Comprobar que se envían los datos por POST y guardarlos en variables
        if ($request->isMethod('POST')) {
            #Recoger los datos comunes a un cliente habitual y a una empresa
            $fullName = $request->request->get('nombre');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirmar = $request->request->get('passwordConfirmar');

            $usuarioExiste = $clienteRepository->findRegisteredClient($email);
            if ($usuarioExiste) {
                return $this->render('registro/registro.html.twig', [
                    "error" => true,
                    "titulo" => "Error",
                    "mensaje" => "El usuario ya existe en nuestra base de datos.",
                    "boton" => true,
                    "mensajeBtn" => "¿Quieres iniciar sesión?",
                    "enlaceBtn" => "login"
                ]);
            } else {
                #Separar el nombre completo a nombre/apellido/apellido
                $arrNombreApellidos = explode(' ', $fullName);
                $name = $arrNombreApellidos[0];
                $lastName1 = $arrNombreApellidos[1] ?? "";
                $lastName2 = $arrNombreApellidos[2] ?? "";

                #Comprobación de que la contraseña esté confirmada correctamente: si está bien se ejecuta la lógica
                $confirmar = $this->comprobarPassword($password, $passwordConfirmar);
                if ($confirmar) {
                    #TODO: Añadir qué pasa cuando no se introducen valores correctos (aunque creo que html lo pilla ya) o repetidos (ya existe la cuenta)
                    if ($_POST['nombreEmpresa'] !== "" && $_POST['nifCif'] !== "") {
                        #Si se incluyen en el formulario los datos exclusivos a una empresa, se tratará como empresa
                        $nombreEmpresa = $request->request->get('nombreEmpresa');
                        $nifCif = $request->request->get('nifCif');

                        #Creación del objeto de tipo Empresa
                        $empresaLogueada = new Empresa();
                        $empresaLogueada->setNombreCompleto($fullName)
                            ->setNombre($name)
                            ->setApellido1($lastName1)
                            ->setApellido2($lastName2)
                            ->setEmail($email)
                            ->setPassword($password)
                            ->setNombreEmpresa($nombreEmpresa)
                            ->setNifCif($nifCif);

                        #Se prepara el objeto para insertarlo en la base de datos
                        $entityManager->persist($empresaLogueada);
                    } else {
                        #Creación del objeto de tipo Empresa
                        $usuarioLogueado = new Cliente();
                        $usuarioLogueado->setNombreCompleto($fullName)
                            ->setNombre($name)
                            ->setApellido1($lastName1)
                            ->setApellido2($lastName2)
                            ->setEmail($email)
                            ->setPassword($password);

                        #Se prepara el objeto para insertarlo en la base de datos
                        $entityManager->persist($usuarioLogueado);
                    }
                    #TODO avisar de haberse registrado correctamente

                    #Se insertan en la base de datos cualquier persist ejecutado
                    $entityManager->flush();
                    #Redirigir al index
                    return $this->redirectToRoute("index");
                } else {
                    return $this->render('registro/registro.html.twig', [
                        "error" => true,
                        "titulo" => "Error",
                        "mensaje" => "Las contraseñas no coinciden. Por favor, revísalas e inténtalo de nuevo.",
                        "boton" => false,
                        "mensajeBtn" => "",
                        "enlaceBtn" => ""
                    ]);
                }
            }
        }

        #Cualquier función que maneje la lógica principal de una vista debe devolver una plantilla twig (lo que se ve en la vista)
        return $this->render('registro/registro.html.twig', [
            "error" => false,
            "titulo" => "",
            "mensaje" => "",
            "boton" => false,
            "mensajeBtn" => "",
            "enlaceBtn" => ""
        ]);
    }

    #Función que maneja la lógica de comprobar que se haya introducido bien la contraseña
    private function comprobarPassword($password, $passwordRepetida): bool
    {
        if ($password != $passwordRepetida) {
            return false;
        } else {
            return true;
        }
    }
}