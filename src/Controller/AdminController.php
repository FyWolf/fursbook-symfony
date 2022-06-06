<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $userUsername = $this->getUser()->getUsername();
        $userProfilePicture = $this->getUser()->getProfilePicture();


        if($request->isXmlHttpRequest())
        {
            if($_POST['action'] == 'switch') {
                if($_POST['pageName'] == 'userList') {

                    $userRepos = $doctrine->getRepository(User::class);
                    $list = $userRepos->adminGetUsers(0);
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/usersList.html.twig', [
                            'list' => $list,
                        ]),
                        )
                    );
                    return $response;
                }
            }
        }

        // TODO: When support need to swap mail of a user, the password need to be re-hashed to be valid,
        // TODO: and so on, a new temporary password needs to be used for the hash and to be given to the user.
        return $this->render('fursbook/admin/home.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
        ]);
    }
}