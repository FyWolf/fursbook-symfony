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

class SettingsController extends AbstractController
{
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