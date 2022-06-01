<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class LangController extends AbstractController
{
    #[Route('/lang', name: 'lang')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest())
        {
            if($this->getUser()) {
                $user = $this->getUser();
                $request->getSession()->set('_locale', $_POST['lang']);
                $user->setLocale($_POST['lang']);
                $entityManager->persist($user);
                $entityManager->flush();

                $response = new JsonResponse();
                $response->setData(array(
                  'done' => true
                  )
                );
                return $response;
            }
            else {
                setcookie('lang', $_POST['lang'], time() + (10 * 365 * 24 * 60 * 60));

                $response = new JsonResponse();
                $response->setData(array(
                  'done' => true
                  )
                );
                return $response;
                dump('test');
            }
        }
        else {
            return $this->redirectToRoute('home_fursbook');
        }
    }
}