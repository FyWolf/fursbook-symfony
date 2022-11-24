<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Newsletter;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }
        else {
            $userUsername = "";
            $userProfilePicture = "";
            if(isset($_COOKIE['lang'])) {
                setlocale(LC_TIME, $_COOKIE['lang']);
            }
            else {
                setlocale(LC_TIME, 'en');
            }
        }

        if(isset($_COOKIE['darkMode'])) {
            if($_COOKIE['darkMode'] == 'true') {
                $darkMode = true;
            }
            else {
                $darkMode = false;
            }
        }
        else {
            $darkMode = false;
        }

        if($request->isXmlHttpRequest())
        {
            if($_POST['action'] == 'scroll') {
                $start= $_POST['offset'];
                $newsRepo = $doctrine->getRepository(Newsletter::class);
                $resultNews = $newsRepo->getAllNews($doctrine, $start);

                $list = $this->renderView('fursbook/scrollNews.html.twig', [
                    'loggedUserUsername' => $userUsername,
                    'loggedUserProfilePicture' => $userProfilePicture,
                    'newsletters' => $resultNews,
                    'darkMode' => $darkMode,
                ]);

                $response = new JsonResponse();
                $response->setData(array(
                    'newsList' => $list
                    )
                );
                return $response;
            }
        }

        $newsRepo = $doctrine->getRepository(Newsletter::class);
        $news = $newsRepo->getAllNews($doctrine, 0);

        return $this->render('fursbook/newsletter.html.twig', [
            'loggedUserProfilePicture' => $userProfilePicture,
            'loggedUserUsername' => $userUsername,
            'darkMode' => $darkMode,
            'newsletters' => $news,
        ]);
    }
}
