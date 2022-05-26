<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Posts;
use App\Entity\Likes;
use App\Entity\User;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile404_fursbook')]
    public function profile404(): Response
    {
        return $this->redirectToRoute('home_fursbook');

    }

    #[Route("/profile/{username}", name: 'profile_fursbook')]
    public function profile(ManagerRegistry $doctrine, string $username, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }
        else {
            $userUsername = "";
            $userProfilePicture = "";
        }
        $repository = $doctrine->getRepository(User::class);
        $postRepo = $doctrine->getRepository(Posts::class);
        $showedUser = $repository->findOneBy(['username' => $username]);

        if($request->isXmlHttpRequest() && $showedUser)
        {
            if($_POST['action'] == 'scroll') {
                $start= $_POST['offset'];
                $foundPosts = $postRepo->findAllPostsById($showedUser->getId(), $start);
                $resultPosts = [];

                foreach ($foundPosts as $result) {
                    $userRepo = $doctrine->getRepository(User::class);
                        $likeRepos = $doctrine->getRepository(Likes::class);
                        $user = $userRepo->findOneBy(['id' => $result->getOwner()]);
                        if($this->getUser()){
                            $foundLike = $likeRepos->checkIfLiked($result->getId(), $this->getUser()->getId());
                            if($foundLike) {
                                $liked = true;
                            }
                            else {
                                $liked = false;
                            }
                        }
                        else {
                            $liked = false;
                        }
                        $countLike = $likeRepos->countLikes($result->getId());

                        $constructedResult = (object) [
                            'ownerProfilePicture' => $user->getProfilePicture(),
                            'ownerUsername' => $user->getUsername(),
                            'postId' => $result->getId(),
                            'isLiked' => $liked,
                            'nbLikes' => $countLike,
                            'content' => $result->getContent(),
                            'nbPictures' => $result->getNbPictures(),
                            'picture1' => $result->getPicture1(),
                            'picture2' => $result->getPicture2(),
                            'picture3' => $result->getPicture3(),
                            'picture4' => $result->getPicture4(),
                            'date' => date('h:i d M Y', intval($result->getDatePosted())),
                        ];

                    array_push($resultPosts, $constructedResult);
                }

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


        if ($showedUser) {
            $foundPosts = $postRepo->findAllPostsById($showedUser->getId(), 0);
            $resultPosts = [];

            foreach ($foundPosts as $result) {
                $userRepo = $doctrine->getRepository(User::class);
                    $likeRepos = $doctrine->getRepository(Likes::class);
                    $user = $userRepo->findOneBy(['id' => $result->getOwner()]);
                    if($this->getUser()){
                        $foundLike = $likeRepos->checkIfLiked($result->getId(), $this->getUser()->getId());
                        if($foundLike) {
                            $liked = true;
                        }
                        else {
                            $liked = false;
                        }
                    }
                    else {
                        $liked = false;
                    }
                    $countLike = $likeRepos->countLikes($result->getId());

                    $constructedResult = (object) [
                        'ownerProfilePicture' => $user->getProfilePicture(),
                        'ownerUsername' => $user->getUsername(),
                        'postId' => $result->getId(),
                        'isLiked' => $liked,
                        'nbLikes' => $countLike,
                        'content' => $result->getContent(),
                        'nbPictures' => $result->getNbPictures(),
                        'picture1' => $result->getPicture1(),
                        'picture2' => $result->getPicture2(),
                        'picture3' => $result->getPicture3(),
                        'picture4' => $result->getPicture4(),
                        'date' => date('h:i d M Y', intval($result->getDatePosted())),
                    ];

                array_push($resultPosts, $constructedResult);
            }

            
            $isUserValid = true;
            return $this->render('fursbook/profile.html.twig', [
                'loggedUserUsername' => $userUsername,
                'showedUserUsername' => $showedUser->getusername(),
                'showedUserProfilePicture' => $showedUser->getProfilePicture(),
                'showedUserProfileBanner' => $showedUser->getProfileBanner(),
                'showedUserBio' => $showedUser->getBio(),
                'isUserValid' => $isUserValid,
                'loggedUserProfilePicture' => $userProfilePicture,
                'posts' => $resultPosts,
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