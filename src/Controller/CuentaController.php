<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CuentaController extends AbstractController
{
    #[Route('/cuenta', name: 'cuenta')]
    public function cuenta(SessionInterface $session, UsuarioRepository $usuarioRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');
        if (!$id || !is_numeric($id)) {
            return $this->redirectToRoute('error');
        }

        $usuarioRegistrado = $usuarioRepository->findOneById($id);
        if (!$usuarioRegistrado) {
            return $this->redirectToRoute('error');
        }

        $idUsuarioRegistrado = $session->get('id');
        if ($id != $idUsuarioRegistrado) {
            return $this->redirectToRoute("cuenta", ["id" => $usuarioRegistrado->getId()]);
        }

        $showModal = $request->query->get('updated') === '1';

        if ($request->isMethod('POST') && isset($_POST['submit'])) {
            $usuarioModificado=$this->guardarDatosCliente($request, $session, $usuarioRegistrado);

            #Actualizar los datos en la base de datos
            $entityManager->persist($usuarioModificado);
            $entityManager->flush();

            return $this->redirectToRoute('cuenta', [
                'id' => $usuarioRegistrado->getId(),
                'updated' => 1,
            ]);
        }

        return $this->render('cuenta/cuenta.html.twig', [
            'usuarioRegistrado' => $usuarioRegistrado,
            'idUsuarioRegistrado' => $idUsuarioRegistrado,
            'submit' => $showModal,
        ]);
    }

    public function guardarDatosCliente(Request $request, SessionInterface $session, Usuario $usuario): Usuario
    {
        #Recoger en variables los datos introducidos en el formulario
        $campos = [
            'email', 'password', 'sexo', 'nombre', 'apellido', 'fechaNacimiento',
            'domicilio', 'portal', 'piso', 'puerta', 'cp', 'localidad', 'provincia',
            'telefono1', 'telefono2'
        ];

        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = $request->request->get($campo) ? $request->request->get($campo) : null;
            $session->set("$campo", $datos[$campo]);
        }

        #Dirección completa
        $arrDireccion = [
            'domicilio' => $datos['domicilio'],
            'portal' => $datos['portal'],
            'piso' => $datos['piso'],
            'puerta' => $datos['puerta'],
            'cp' => $datos['cp'],
            'localidad' => $datos['localidad'],
            'provincia' => $datos['provincia']
        ];

        #Separar apellidos
        $arrNombreApellidos = explode(' ', $datos['apellido']);
        $datos['apellido1'] = $arrNombreApellidos[0] ?? "";
        $datos['apellido2'] = $arrNombreApellidos[1] ?? "";

        #Settear los datos introducidos al usuario registrado
        $usuario->setEmail($datos['email'])
            ->setPassword($datos['password'])
            ->setSexo($datos['sexo'])
            ->setNombre($datos['nombre'])
            ->setApellido1($datos['apellido1'])
            ->setApellido2($datos['apellido2'])
            ->setNombreCompleto($datos['nombre'] . " " . $datos['apellido'])
            ->setFechaNacimiento(new \DateTime($datos['fechaNacimiento']))
            ->setDomicilio($arrDireccion)
            ->setTelefono1($datos['telefono1'])
            ->setTelefono2($datos['telefono2']);

        return $usuario;
    }

    #[Route('/cuenta/eliminar', name: 'eliminar-cuenta', methods: ['POST'])]
    public function eliminarCuenta(SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        $idUsuario = $session->get('id');

        $usuarioBorrar = $entityManager->getRepository(Usuario::class)->findOneById($idUsuario);

        $usuarioBorrar->setEliminado(true);
        $entityManager->persist($usuarioBorrar);
        $entityManager->flush();

        $request->getSession()->invalidate();

        return $this->redirectToRoute("index");

    }
}