<?php
// src/Controller/ClienteController.php

namespace App\Controller;

use App\Repository\ClienteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
}
