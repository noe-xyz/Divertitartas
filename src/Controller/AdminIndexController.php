<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminIndexController extends AbstractController
{
    #[Route('/admin', name: 'AdminIndex')]
    public function index(SessionInterface $session): Response
    {

        return $this->render('index/index.html.twig' );
    }
}