<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LangController extends AbstractController
{
    #[Route('/lang', name: 'lang')]
    public function index(Request $request): Response
    {
        if(!empty($_POST) && isset($_POST)) {
            $request->getSession()->set('_locale', $_POST['lang']);
            return $this->redirectToRoute($_POST['refer']);
        }
        return $this->redirectToRoute('home_fursbook');
    }
}