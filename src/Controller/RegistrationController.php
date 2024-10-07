<?php 

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface; // Importer le MailerInterface
use Symfony\Component\Mime\Email; // Importer la classe Email

class RegistrationController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer; // Ajouter le MailerInterface

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer; // Initialiser le MailerInterface
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($hashedPassword);
            $user->setEmail($form->get('email')->getData()); // Assurez-vous de définir l'email

            // Attribuer le rôle ROLE_USER à l'utilisateur
            $user->addRole('ROLE_USER');

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Envoi d'un e-mail de confirmation
            $this->sendConfirmationEmail($user->getEmail());

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function sendConfirmationEmail(string $email): void
    {
        $emailMessage = (new Email())
            ->from('dnicolas120@gmail.com') // Remplacez par votre adresse e-mail
            ->to($email)
            ->subject('Confirmation d\'inscription')
            ->text('Merci pour votre inscription!');

        try {
            $this->mailer->send($emailMessage);
        } catch (\Exception $e) {
            // Log ou afficher un message d'erreur
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }
    }
}
