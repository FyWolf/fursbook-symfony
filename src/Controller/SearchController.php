<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Form\SearchType;
use App\Entity\User;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
            setlocale(LC_TIME, $this->getUser()->getLocale());
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

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('searchId', ['search' => $form->get('search')->getData()]);
        }

        return $this->render('fursbook/search.html.twig', [
            'form' => $form->createView(),
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
            'resultArray' => [],
            'actualSearch' => '',
                ]);
            }

    #[Route("/search/{search}", name: 'searchId')]
    public function searchId(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository, string $search): Response
    {
        unset($form);
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
            setlocale(LC_TIME, $this->getUser()->getLocale());
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
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('searchId', ['search' => $form->get('search')->getData()]);
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

        $foundUsers = $userRepository->findByUsername('%'.$search.'%');

        return $this->render('fursbook/search.html.twig', [
        'form' => $form->createView(),
        'loggedUserUsername' => $userUsername,
        'loggedUserProfilePicture' => $userProfilePicture,
        'resultArray' => $foundUsers,
        'actualSearch' => $search,
        'darkMode' => $darkMode,
        ]);
    }
}