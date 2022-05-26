<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\MailValidationSettingsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use Gedmo\Sluggable\Util\Urlizer;
use App\Form\ProfileSettingsType;
use App\Entity\Posts;
use App\Entity\User;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'settings_fursbook')]
    public function settings(Request $request, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        $user = $this->getUser();
        $userUsername = $this->getUser()->getUsername();
        $userProfilePicture = $this->getUser()->getProfilePicture();
        $profileForm = $this->createForm(ProfileSettingsType::class);
        $profileForm->handleRequest($request);
        if ($profileForm->isSubmitted()){
            if (null !== $profileForm->get('username')->getData()){
                $user->setUsername( $form->get('username')->getData());
            }
            if (null !== $profileForm->get('bio')->getData()){
                $user->setBio( $profileForm->get('bio')->getData());
            }
            if (null !== $profileForm->get('profilePicture')->getData()){
                $uploadedFile = $profileForm->get('profilePicture')->getData();
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
            if (null !== $profileForm->get('profileBanner')->getData()){
                $uploadedFile = $profileForm->get('profileBanner')->getData();
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

        $userForm = $this->createForm(MailValidationSettingsType::class);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted()){
            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_mail_verify',
                $this->getUser()->getId(),
                $this->getUser()->getEmail(),
                ['id' => $this->getUser()->getId()]
            );

            $dsn = $this->getParameter('dsn');

            $transport = Transport::fromDsn($dsn);

            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from('no-reply@fursbook.org')
                ->to($this->getUser()->getEmail())
                ->subject('Email confirmation')
                ->html('
                <p>Welcome to Fursbook '.$this->getUser()->getUsername().' !</p>
                <p>To verify your account, please open the link:</p>
                <a href="'.$signatureComponents->getSignedUrl().'">Verify my account</a>
                ');

            $mailer->send($email);
        }
        return $this->render('fursbook/settings.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
            'profileForm' => $profileForm->createView(),
            'userForm' => $userForm->createView()
        ],);
    }
}