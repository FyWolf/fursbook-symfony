<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ProfileController extends AbstractController{
    #[Route("/profile/{username}", name: 'profile_fursbook')]
    public function profile(ManagerRegistry $doctrine, string $username): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }

        $repository = $doctrine->getRepository(User::class);
        $showedUser = $repository->findOneBy(['username' => $username]);

        if ($showedUser) {
            $isUserValid = true;
            return $this->render('fursbook/profile.html.twig', [
                'loggedUserUsername' => $userUsername,
                'showedUserUsername' => $showedUser->getusername(),
                'showedUserProfilePicture' => $showedUser->getProfilePicture(),
                'isUserValid' => $isUserValid,
                'loggedUserProfilePicture' => $userProfilePicture,
            ],);
        }

        else {
            $isUserValid = false;
            return $this->render('fursbook/profile.html.twig', [
                'loggedUserUsername' => $userUsername,
                'isUserValid' => $isUserValid,
                'loggedUserProfilePicture' => $userProfilePicture,
            ],);
        };
    }
}