<?php

namespace App\Controller;

use App\Entity\Cliente;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistroController extends AbstractController
{
    #[Route('/registro', name: 'registro')]
    public function registro(EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $name = $request->request->get('nombre');
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $usuarioLogueado = new Cliente();
            $usuarioLogueado->setNombre($name);
            $usuarioLogueado->setEmail($email);
            $usuarioLogueado->setPassword($password);

            $entityManager->persist($usuarioLogueado);
            $entityManager->flush();

        }

        return $this->render('registro/registro.html.twig');
    }
}