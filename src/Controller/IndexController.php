<?php

namespace App\Controller;

use App\Entity\Valoraciones;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $usuarioRegistrado = $session->get('nombreCompleto');
        $id = $session->get('id');

        $valoraciones = $entityManager->getRepository(Valoraciones::class)->findBy([], ['id' => 'DESC'], 10);

        return $this->render('index/index.html.twig', [
            'usuarioRegistrado' => $usuarioRegistrado,
            'id' => $id,
            'valoraciones' => $valoraciones
        ]);
    }

    #[Route('/valorar', name: 'valorar')]
    public function valorar(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $campos = ['nombreValoracion', 'rating', 'comentarioValoracion'];
        $datos = [];
        foreach ($campos as $campo) {
            $datos[$campo] = $request->request->get($campo);
        }

        $valoracion = new Valoraciones();
        $valoracion->setEstrellas($datos['rating'])
            ->setUsuario($datos['nombreValoracion'])
            ->setComentario($datos['comentarioValoracion']);

        $entityManager->persist($valoracion);
        $entityManager->flush();

        return new RedirectResponse($this->generateUrl('index') . '#comentarios');
    }

}