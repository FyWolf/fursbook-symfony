<?php

namespace App\Controller;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Entity\ProfileReports;
use App\Entity\PostsReports;
use App\Entity\Newsletter;
use App\Entity\Posts;
use App\Entity\User;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $userUsername = $this->getUser()->getUsername();
        $userProfilePicture = $this->getUser()->getProfilePicture();

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

                elseif($_POST['pageName'] == 'usersReported') {
                    $RepUserRepos = $doctrine->getRepository(ProfileReports::class);
                    $list = $RepUserRepos->adminGetReportedUsers(0);
                    $users = $RepUserRepos->countReportedUsers();
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/usersReported.html.twig', [
                            'list' => $list,
                        ]),
                        'userCount' => $users['COUNT(*)'],
                        )
                    );
                    return $response;
                }

                elseif($_POST['pageName'] == 'postsReported') {
                    $RepPostRepos = $doctrine->getRepository(PostsReports::class);
                    $list = $RepPostRepos->adminGetReportedPosts(0);
                    $posts = $RepPostRepos->countReportedPosts();
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/postsReported.html.twig', [
                            'list' => $list,
                        ]),
                        'userCount' => $posts['COUNT(*)'],
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

                elseif($_POST['pageName'] == 'managePostReport') {
                    $postRepo = $doctrine->getRepository(Posts::class);
                    $resultPosts = $postRepo->getPostById($doctrine, $this->getUser(), $_POST['id']);
                    $RepPostRepos = $doctrine->getRepository(PostsReports::class);
                    $report = $RepPostRepos->selectAllReportById($_POST['id']);
                    $mainReport = $RepPostRepos->selectReportById($_POST['id']);
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/managePostReport.html.twig', [
                            'report' => $report,
                            'mainReport' => $mainReport,
                            'post' => $resultPosts,
                            'darkMode' => $darkMode,
                        ]),
                        )
                    );
                    return $response;
                }

                elseif($_POST['pageName'] == 'createUser') {
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/createUser.html.twig'),
                        )
                    );
                    return $response;
                }

                elseif($_POST['pageName'] == 'newNews') {
                    $response = new JsonResponse();
                    $response->setData(array(
                        'page' => $this->renderView('fursbook/admin/pannel/newNewsLetter.html.twig'),
                        )
                    );
                    return $response;
                }
            }

            elseif($_POST['action'] == 'createUser') {
                $user = new User;
                $user->setEmail($_POST['email']);
                $hashedPassword = $userPasswordHasher->hashPassword($user, $_POST['password']);
                $userRepos->adminCreateUser($_POST['email'], $hashedPassword, $_POST['username'], $_POST['pfp'], $_POST['bio'], $_POST['banner']);
                $response = new JsonResponse();
                $response->setData(array(
                    )
                );
                return $response;
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
                    "newPass" => $randomPassword,
                    )
                );
                return $response;
            }

            elseif($_POST['action'] == 'setUsername') {
                if (strpos($_POST['username'], "../") !== false) {
                    $response = new JsonResponse();
                    $response->setData(array(
                        "error" => true,
                        )
                    );
                    return $response;
                } else {
                    $userRepos->setUsernameViaID($_POST['id'], $_POST['username']);
                }
                $response = new JsonResponse();
                $response->setData(array(
                    )
                );
                return $response;
            }

            elseif($_POST['action'] == 'checkUsername') {
                $match = $userRepos->checkUsername($_POST['username']);
                $response = new JsonResponse();
                if($match) {
                    $response->setData(array(
                        'match' => true,
                        )
                    );
                    return $response;
                }
                else {
                    $response->setData(array(
                        'match' => false,
                        )
                    );
                    return $response;
                }
            }

            elseif($_POST['action'] == 'checkEmail') {
                $match = $userRepos->checkEmail($_POST['mail']);
                $response = new JsonResponse();
                if($match) {
                    $response->setData(array(
                        'match' => true,
                        )
                    );
                    return $response;
                }
                else {
                    $response->setData(array(
                        'match' => false,
                        )
                    );
                    return $response;
                }
            }

            elseif($_POST['action'] == 'uploadNewsLetterImage') {
                $uploadedFile = $request->files->get('file');
                $destination = $this->getParameter('kernel.project_dir').'/public/ressources/images/newsletter/';
                $fileName = $_POST['key'];

                $uploadedFile->move(
                    $destination,
                    $fileName
                );

                $response = new JsonResponse();
                $response->setData(array(
                    'done' => true,
                    )
                );
                return $response;
            }

            elseif($_POST['action'] == 'uploadNewsLetter') {
                if($_POST['checkmark'] == 'true')
                {
                    $users = $userRepos->getSubscribedUsers();
                    $dsn = $this->getParameter('dsn');
                    $transport = Transport::fromDsn($dsn);
                    $mailer = new Mailer($transport);
                    foreach ($users as $user) {
                        $content = str_replace('%username%', $user->getUsername(), $_POST['newsletterMailContent']);
                        $email = (new Email())
                            ->from('newsletter@fursbook.org')
                            ->to($user->getEmail())
                            ->subject($_POST['newsletterMailName'])
                            ->html($this->render('email/newsletter.html.twig', ['content' => $content,])->getContent());
                        $mailer->send($email);
                    }
                }

                $newsletter = new Newsletter;
                $newsletter->setTitle($_POST['newsletterName']);
                $newsletter->setContent($_POST['newsletterContent']);
                $newsletter->setDate(time());
                $entityManager->persist($newsletter);
                $entityManager->flush();

                $response = new JsonResponse();
                $response->setData(array(
                    'done' => true,
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