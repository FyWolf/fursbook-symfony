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
use App\Entity\User;

class FursbookController extends AbstractController
{
    #[Route('/', name: 'home_fursbook')]
    public function home(ManagerRegistry $doctrine, Request $request): Response
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
        $foundPosts = $postRepo->findAllPosts(0);
        $resultPosts = [];

        foreach ($foundPosts as $result) {
            $userRepo = $doctrine->getRepository(User::class);
            $user = $userRepo->findOneBy(['id' => $result->getOwner()]);
            $constructedResult = (object) [
                'ownerProfilePicture' => $user->getProfilePicture(),
                'ownerUsername' => $user->getUsername(),
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

        if($request->isXmlHttpRequest())
        {
            $start= $_POST['offset'];
            $postRepo = $doctrine->getRepository(Posts::class);
            $foundPosts = $postRepo->findAllPosts($start);
            $resultPosts = [];

            foreach ($foundPosts as $result) {
                $userRepo = $doctrine->getRepository(User::class);
                $user = $userRepo->findOneBy(['id' => $result->getOwner()]);
                $constructedResult = (object) [
                    'ownerProfilePicture' => $user->getProfilePicture(),
                    'ownerUsername' => $user->getUsername(),
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
              'classifiedList' => $list
              )
            );
            return $response;
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

    #[Route('/settings', name: 'settings_fursbook')]
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userUsername = $this->getUser()->getUsername();
        $userProfilePicture = $this->getUser()->getProfilePicture();
        $form = $this->createForm(SettingsType::class);
        $form->handleRequest($request);
        $message = '';
        if ($form->isSubmitted())
        {
            if (null !== $form->get('username')->getData()){
                $user->setUsername( $form->get('username')->getData());
            }
            if (null !== $form->get('bio')->getData()){
                $user->setBio( $form->get('bio')->getData());
            }
            if (null !== $form->get('profilePicture')->getData()){
                $uploadedFile = $form->get('profilePicture')->getData();
                $destination = $this->getParameter('kernel.project_dir').'/public/userRessources/'.$user->getId().'/profilePictures';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $user->getUsername().'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $userProfilePicture = '/userRessources/'.$user->getId().'/profilePictures/'.$newFilename;
                $user->setProfilePicture($userProfilePicture);
            }
            if (null !== $form->get('profileBanner')->getData()){
                $uploadedFile = $form->get('profileBanner')->getData();
                $destination = $this->getParameter('kernel.project_dir').'/public/userRessources/'.$user->getId().'/profileBanner';
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $user->getUsername().'-'.uniqid().'.'.$uploadedFile->guessExtension();

                $uploadedFile->move(
                    $destination,
                    $newFilename
                );
                $userProfileBanner = '/userRessources/'.$user->getId().'/profileBanner/'.$newFilename;
                $user->setProfileBanner($userProfileBanner);
            }
            $message = 'Modifications enregistrées';
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();

            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('fursbook/settings.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
            'form' => $form->createView(),
            'message' => $message,
        ],);
    }
}