<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Empresa;
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
            $fullName = $request->request->get('nombre');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            #TODO lógica confirmar contraseña

            if (isset($_POST['nombreEmpresa']) && isset($_POST['nifCif'])) {
                $nombreEmpresa = $request->request->get('nombreEmpresa');
                $nifCif = $request->request->get('nifCif');

                $empresaLogueada = new Empresa();
                $empresaLogueada->setNombreCompleto($fullName);
                $empresaLogueada->setEmail($email);
                $empresaLogueada->setPassword($password);
                $empresaLogueada->setNombreEmpresa($nombreEmpresa);
                $empresaLogueada->setNifCif($nifCif);

                $entityManager->persist($empresaLogueada);
            } else {
                $usuarioLogueado = new Cliente();
                $usuarioLogueado->setNombreCompleto($fullName);
                $usuarioLogueado->setEmail($email);
                $usuarioLogueado->setPassword($password);

                $entityManager->persist($usuarioLogueado);
            }

            $entityManager->flush();
        }

        return $this->render('registro/registro.html.twig');
    }
}