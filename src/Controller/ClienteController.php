<?php
// src/Controller/ClienteController.php

namespace App\Controller;

use App\Entity\Trabajador;
use App\Repository\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClienteController extends AbstractController
{
    #[Route('/admin/gestion-usuarios', name: 'gestion_usuarios')]
    public function listar(ClienteRepository $clienteRepository): Response
    {
        $clientes = $clienteRepository->findAll();

        return $this->render('admin/gestion_usuarios.html.twig', [
            'clientes' => $clientes,
        ]);
    }

    #[Route('/admin/eliminar-cliente/{id}', name: 'eliminar_cliente')]
    public function eliminar(int $id, ClienteRepository $clienteRepository, EntityManagerInterface $entityManager): Response
    {
        $cliente = $clienteRepository->find($id);
        if ($cliente) {
            $entityManager->remove($cliente);
            $entityManager->flush();
        }
        return $this->redirectToRoute('gestion_usuarios');
    }

    #[Route('/administrador/crear-trabajador', name: 'crear_trabajador')]
    public function crearTrabajador(EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->getMethod() == 'POST' && isset($_POST['submit'])) {
            #Recoger en variables los datos introducidos en el formulario
            $email = $request->request->get('email');
            $password = $request->request->get('pass');
            $nombre = $request->request->get('nombreCompleto');
            $telefono = $request->request->get('telefono');
            $turno = $request->request->get('turno');
            $puesto = $request->request->get('puesto');

            $trabajador = new Trabajador();
            $trabajador->setEmail($email)
                ->setPassword($password)
                ->setNombreCompleto($nombre)
                ->setTelefono1($telefono)
                ->setTurno($turno)
                ->setPuesto($puesto);
            $entityManager->persist($trabajador);
            $entityManager->flush();
        }

        return $this->render('temp.html.twig');
    }
}
