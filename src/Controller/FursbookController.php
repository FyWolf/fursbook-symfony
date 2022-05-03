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
            $userUsername = $this->getUser()->getUsername();
        }
        else {
            $userUsername = "empty";
        }
        return $this->render('fursbook/home.html.twig', [
            'loggedUserUsername' => $userUsername,
        ],);
    }

    #[Route('/recovery', name: 'recovery_fursbook')]
    public function recovery(): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
        }
        else {
            $userUsername = "empty";
        }
        return $this->render('fursbook/recovery.html.twig', [
            'loggedUserUsername' => $userUsername,
        ],);
    }
}