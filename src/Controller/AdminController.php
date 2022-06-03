<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        // TODO: When support need to swap mail of a user, the password need to be re-hashed to be valid,
        // TODO: and so on, a new temporary password needs to be used for the hash and to be given to the user.
        return $this->render('fursbook/admin/home.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
        ]);
    }
}