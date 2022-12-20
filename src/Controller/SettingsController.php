<?php

namespace App\Controller;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use App\Form\ProfileSettingsType;
use App\Form\UserSettingsType;
use App\Entity\Posts;
use App\Entity\User;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'settings_fursbook')]
    public function settings(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        $user = $this->getUser();
        $userUsername = $this->getUser()->getUsername();
        $userProfilePicture = $this->getUser()->getProfilePicture();
        $profileForm = $this->createForm(ProfileSettingsType::class);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted()){
            if (null !== $profileForm->get('username')->getData()){
                if (strpos($profileForm->get('username')->getData(), "../") !== false) {
                    return $this->redirectToRoute('settings_fursbook');
                } else {
                    $user->setUsername( $profileForm->get('username')->getData());
                }
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
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();

            $entityManager->persist($user);
            $entityManager->flush();
        }

        if($request->isXmlHttpRequest()) {
            if ($_POST['action'] == 'mailVerify'){
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
                    <div style="background-color: #261d2f; text-align: center; color: white; font-size: 1.3em; max-width: 600px; border-radius: 5px; margin: auto; padding: 10px;">
                        <p>Welcome to Fursbook '.$this->getUser()->getUsername().' !</p>
                        <p>To verify your account, please open the link:</p>
                        <a href="'.$signatureComponents->getSignedUrl().'" style="color: white; background-color: #311f3f; border: 1px solid white; padding: 0.5vh 1vh; border-radius: 10px; font-size: 1rem; cursor: pointer; text-decoration: none;">Verify my account</a>
                    <div>
                    ');

                $mailer->send($email);

                $response = new JsonResponse();
                $response->setData(array(
                    'done' => true,
                    )
                );
                return $response;
            }

            elseif ($_POST['action'] == 'setNewPassword'){
                if ($userPasswordHasher->isPasswordValid($this->getUser(), $_POST['oldPwd'])) {
                    $user = $this->getUser();
                    $user->setPassword($userPasswordHasher->hashPassword($user, $_POST['newPwd']));

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $response = new JsonResponse();
                    $response->setData(array(
                            'done' => true,
                        )
                    );
                    return $response;
                };
            }

            elseif ($_POST['action'] == 'setNewMail'){
                if ($userPasswordHasher->isPasswordValid($this->getUser(), $_POST['oldPwd'])) {
                    $dsn = $this->getParameter('dsn');
                    $transport = Transport::fromDsn($dsn);
                    $mailer = new Mailer($transport);
                    $user = $this->getUser();

                    $email = (new Email())
                        ->from('no-reply@fursbook.org')
                        ->to($this->getUser()->getEmail())
                        ->subject('Email changed')
                        ->html('
                        <div style="background-color: #261d2f; text-align: center; color: white; font-size: 1.3em; max-width: 600px; border-radius: 5px; margin: auto; padding: 10px;">
                            <p>Hello '.$this->getUser()->getUsername().'</p>
                            <p>Your email adress onf Fursbook has been changed to that address:</p>
                            <p>'.$_POST['newMail'].'</p>
                        </div>
                        ');

                    $mailer->send($email);

                    $user->setEmail($_POST['newMail']);
                    $user->setPassword($userPasswordHasher->hashPassword($user, $_POST['oldPwd']));
                    $user->setIsVerified(false);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $response = new JsonResponse();
                    $response->setData(array(
                            'done' => true,
                        )
                    );
                    return $response;

                    dump('coucou');
                }
            }

            elseif ($_POST['action'] == 'newsletterUnSub'){
                $user = $this->getUser();
                $user->setIsSubscribed(false);
                $entityManager->persist($user);
                $entityManager->flush();

                $response = new JsonResponse();
                $response->setData(array(
                        'done' => true,
                    )
                );
                return $response;
            }

            elseif ($_POST['action'] == 'newsletterSub'){
                $user = $this->getUser();
                $user->setIsSubscribed(true);
                $entityManager->persist($user);
                $entityManager->flush();

                $response = new JsonResponse();
                $response->setData(array(
                        'done' => true,
                    )
                );
                return $response;
            }
        }

        $userForm = $this->createForm(UserSettingsType::class);
        $userForm->handleRequest($request);

        return $this->render('fursbook/settings.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
            'profileForm' => $profileForm->createView(),
            'userForm' => $userForm->createView()
        ],);
    }

    #[Route('/settings/unsubscribe', name: 'newsletter_unsubscribe')]
    public function unsubscribe(EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }
        else {
            $userUsername = "";
            $userProfilePicture = "";
            if(isset($_COOKIE['lang'])) {
                setlocale(LC_TIME, $_COOKIE['lang']);
            }
            else {
                setlocale(LC_TIME, 'en');
            }
        }

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

        $user = $this->getUser();
        if($user->isIsSubscribed() == true)
        {
        $user->setIsSubscribed(false);
        $entityManager->persist($user);
        $entityManager->flush();
        }else {
            return $this->redirectToRoute('home_fursbook');
        }

        return $this->render('fursbook/unsubscribe.html.twig', [
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
            'darkMode' => $darkMode,
        ],);
    }
}