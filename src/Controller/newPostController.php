<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\NewPostType;
use App\Entity\Posts;


class newPostController extends AbstractController
{
    #[Route('/newpost', name: 'newPost')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()->getIsVerified()){
            $form = $this->createForm(NewPostType::class);
            $form->handleRequest($request);
            $userUsername = $this->getUser()->getUsername();
            $userProfilePicture = $this->getUser()->getProfilePicture();

            if ($form->isSubmitted() && $form->isValid()) {
                $post = new Posts();
                $post->setOwner($this->getUser()->getId());
                $post->setDatePosted(time());
                $nbPictures = 0;
                if ($form->get('content')->getData() !== null) {
                    $post->setContent($form->get('content')->getData());
                }

                if (null !== $form->get('image1')->getData()){
                    $uploadedFile = $form->get('image1')->getData();
                    $destination = $this->getParameter('kernel.project_dir').'/public/userRessources/'.$this->getUser()->getId().'/postsPictures';
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $this->getUser()->getUsername().'-'.uniqid().'.'.$uploadedFile->guessExtension();

                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );

                    $postPicture1 = '/userRessources/'.$this->getUser()->getId().'/postsPictures/'.$newFilename;
                    $post->setPicture1($postPicture1);
                    $nbPictures = 1;
                }

                if (null !== $form->get('image2')->getData()){
                    $uploadedFile = $form->get('image2')->getData();
                    $destination = $this->getParameter('kernel.project_dir').'/public/userRessources/'.$this->getUser()->getId().'/postsPictures';
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $this->getUser()->getUsername().'-'.uniqid().'.'.$uploadedFile->guessExtension();

                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );

                    $postPicture2 = '/userRessources/'.$this->getUser()->getId().'/postsPictures/'.$newFilename;
                    $post->setPicture2($postPicture2);
                    $nbPictures = 2;
                }

                if (null !== $form->get('image3')->getData()){
                    $uploadedFile = $form->get('image3')->getData();
                    $destination = $this->getParameter('kernel.project_dir').'/public/userRessources/'.$this->getUser()->getId().'/postsPictures';
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $this->getUser()->getUsername().'-'.uniqid().'.'.$uploadedFile->guessExtension();

                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );

                    $postPicture3 = '/userRessources/'.$this->getUser()->getId().'/postsPictures/'.$newFilename;
                    $post->setPicture3($postPicture3);
                    $nbPictures = 3;
                }

                if (null !== $form->get('image4')->getData()){
                    $uploadedFile = $form->get('image4')->getData();
                    $destination = $this->getParameter('kernel.project_dir').'/public/userRessources/'.$this->getUser()->getId().'/postsPictures';
                    $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $this->getUser()->getUsername().'-'.uniqid().'.'.$uploadedFile->guessExtension();

                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );

                    $postPicture4 = '/userRessources/'.$this->getUser()->getId().'/postsPictures/'.$newFilename;
                    $post->setPicture4($postPicture4);
                    $nbPictures = 4;
                }

                $post->setNbPictures($nbPictures);
                $entityManager->persist($post);
                $entityManager->flush();

                return $this->redirectToRoute('home_fursbook');
            }

            return $this->render('fursbook/newPost.html.twig', [
                'form' => $form->createView(),
                'loggedUserUsername' => $userUsername,
                'loggedUserProfilePicture' => $userProfilePicture,
                  ]);
        }
        else {
            return $this->redirectToRoute('home_fursbook');
        }
    }
}