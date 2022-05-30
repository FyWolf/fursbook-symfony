<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use Gedmo\Sluggable\Util\Urlizer;
use App\Form\SettingsType;
use App\Entity\Posts;
use App\Entity\Likes;
use App\Entity\User;

class FursbookController extends AbstractController
{
    #[Route('/', name: 'home_fursbook')]
    public function home(ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }
        else {
            $userUsername = "";
            $userProfilePicture = "";
        }

        $postRepo = $doctrine->getRepository(Posts::class);
        $resultPosts = $postRepo->getAllPosts($doctrine, 0, $this->getUser());

        if($request->isXmlHttpRequest())
        {
            if($_POST['action'] == 'scroll') {
                $start= $_POST['offset'];
                $postRepo = $doctrine->getRepository(Posts::class);
                $resultPosts = $postRepo->getAllPosts($doctrine, $start, $this->getUser());

                $list = $this->renderView('fursbook/scrollPosts.html.twig', [
                    'loggedUserUsername' => $userUsername,
                    'loggedUserProfilePicture' => $userProfilePicture,
                    'posts' => $resultPosts,
                ]);

                $response = new JsonResponse();
                $response->setData(array(
                  'postsList' => $list
                  )
                );
                return $response;
            }

            if($_POST['action'] == 'like') {
                if($this->getUser()){
                    $like = new Likes;
                    $like->setPostId($_POST['id']);
                    $like->setUserId($this->getUser()->getId());
                    $entityManager->persist($like);
                    $entityManager->flush();

                    $likeRepos = $doctrine->getRepository(Likes::class);
                    $countLikes = $likeRepos->countLikes($_POST['id']);

                    $response = new JsonResponse();
                    $response->setData(array(
                        'likes' => $countLikes,
                        'liked' => true,
                        )
                    );
                    return $response;
                }
            }

            if($_POST['action'] == 'unlike') {
                if($this->getUser()){
                    $likeRepos = $doctrine->getRepository(Likes::class);
                    $like = $likeRepos->checkIfLiked($_POST['id'], $this->getUser()->getId());
                    $entityManager->remove($like);
                    $entityManager->flush($like);
                    $countLikes = $likeRepos->countLikes($_POST['id']);
                    $response = new JsonResponse();
                    $response->setData(array(
                        'likes' => $countLikes,
                        'liked' => true,
                        )
                    );
                    return $response;
                }
            }
        }
        return $this->render('fursbook/home.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
            'posts' => $resultPosts,
        ],);
    }

    #[Route('/recovery', name: 'recovery_fursbook')]
    public function recovery(): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }
        else {
            $userUsername = "";
            $userProfilePicture = "";
        }
        return $this->render('fursbook/recovery.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
        ],);
    }
}