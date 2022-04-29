<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FursbookController extends AbstractController
{
    #[Route('/fursbook', name: 'app_fursbook')]
    public function index(): Response
    {
        return $this->render('fursbook/index.html.twig', [
            'controller_name' => 'FursbookController',
        ]);
    }

    #[Route('/', name: 'fursbook')]
    public function home(): Response
    {
        return $this->render('fursbook/home.html.twig', [
            'controller_name' => 'FursbookController',
        ]);
    }
}
