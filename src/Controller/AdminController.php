<?php

namespace App\Controller;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $userUsername = $this->getUser()->getUsername();
        $userProfilePicture = $this->getUser()->getProfilePicture();


        if($request->isXmlHttpRequest())
        {
            $userRepos = $doctrine->getRepository(User::class);
            if($_POST['action'] == 'switch') {
                if($_POST['pageName'] == 'userList') {
                    $list = $userRepos->adminGetUsers(0);
                    $users = $userRepos->countUsers();
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/usersList.html.twig', [
                            'list' => $list,
                        ]),
                        'userCount' => $users['COUNT(*)'],
                        )
                    );
                    return $response;
                }

                elseif($_POST['pageName'] == 'editProfile') {
                    $user = $userRepos->selectUserViaID($_POST['id']);
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/userProfile.html.twig', [
                            'user' => $user,
                        ]),
                        )
                    );
                    return $response;
                }
            }

            elseif($_POST['action'] == 'deleteUser') {
                $list = $userRepos->deleteUserViaID($_POST['id']);
                $response = new JsonResponse();
                $response->setData(array(
                )
                );
                return $response;
            }

            elseif($_POST['action'] == 'usrListSwitch') {
                $list = $userRepos->adminGetUsers($_POST['offset']);
                $response = new JsonResponse();
                $response->setData(array(
                    'template' => $this->renderView('fursbook/admin/pannel/template/userList.html.twig', [
                        'list' => $list,
                    ]),
                )
                );
                return $response;
            }

            elseif($_POST['action'] == 'setEmail') {
                $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                $pass = array();
                $alphaLength = strlen($alphabet) - 1;
                for ($i = 0; $i < 16; $i++) {
                    $n = rand(0, $alphaLength);
                    $pass[] = $alphabet[$n];
                }
                $randomPassword = implode($pass);

                $user = new User;
                $user->setEmail($_POST['email']);
                $hashedPassword = $userPasswordHasher->hashPassword($user, $randomPassword);

                $userRepos->setEmailViaID($_POST['id'], $_POST['email'], $hashedPassword);

                $user = $userRepos->selectUserViaID($_POST['id']);
                $response = new JsonResponse();
                $response->setData(array(
                    )
                );
                return $response;
            }

            elseif($_POST['action'] == 'setUsername') {
                $userRepos->setUsernameViaID($_POST['id'], $_POST['username']);

                $user = $userRepos->selectUserViaID($_POST['id']);
                $response = new JsonResponse();
                $response->setData(array(
                    )
                );
                return $response;
            }
        }

        return $this->render('fursbook/admin/home.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
        ]);
    }
}