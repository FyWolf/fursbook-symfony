<?php

namespace App\Controller;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            if (strpos($form->get('username')->getData(), "../") !== false) {
                return $this->redirectToRoute('app_register');
            } else {
                $user->setUsername($form->get('username')->getData());
            }
            $user->setEmail($form->get('email')->getData());
            $user->setProfilePicture("/ressources/images/default/profilePicture.png");
            $user->setDateCreated(time());
            $user->setIsSubscribed($form->get('isSubscribed')->getData());
            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_mail_verify',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $dsn = $this->getParameter('dsn');

            $transport = Transport::fromDsn($dsn);

            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from('no-reply@fursbook.org')
                ->to($user->getEmail())
                ->subject('Email confirmation')
                ->html('
                <p>Welcome to Fursbook '.$user->getUsername().' !</p>
                <p>To verify your account, please open the link:</p>
                <a href="'.$signatureComponents->getSignedUrl().'">Verify my account</a>
                ');

            $mailer->send($email);

            return $this->redirectToRoute('home_fursbook');
        }

        if ($this->getUser()) {
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();
        }
        else {
            $userUsername = "";
            $userProfilePicture = "";

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'loggedUserUsername' => $userUsername,
            'loggedUserProfilePicture' => $userProfilePicture,
        ]);
    }

    #[Route('/verify', name: 'app_mail_verify')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager): Response {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        return $this->redirectToRoute('login');
    }
}
