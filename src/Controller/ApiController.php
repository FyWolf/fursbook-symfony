<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Likes;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(ManagerRegistry $doctrine, EntityManagerInterface $entityManager, Request $request): Response
    {
        if($request->isXmlHttpRequest())
        {
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
    }
}
