<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class FursbookController extends AbstractController
{
    #[Route('/', name: 'home_fursbook')]
    public function home(): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser()->getUsername();
        }
        else {
            $user = "empty";
        }
        return $this->render('fursbook/home.html.twig', [
            'obj' => $user,
        ],);
    }

    #[Route('/recovery', name: 'recovery_fursbook')]
    public function recovery(): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser()->getUsername();
        }
        else {
            $user = "empty";
        }
        return $this->render('fursbook/recovery.html.twig', [
            'obj' => $user,
        ],);
    }
}