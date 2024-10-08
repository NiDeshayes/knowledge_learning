<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/checkout/success', name: 'checkout_success')]
    public function success(SessionInterface $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $purchasedItems = []; // Initialisation de la variable pour les éléments achetés

        if ($user) {
            // Récupérer le panier de la session
            $cart = $session->get('cart', []);

            foreach ($cart as $type => $items) {
                foreach ($items as $id => $item) {
                    if ($type === 'lessons') {
                        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
                        if ($lesson) {
                            $user->addRole('ROLE_' . strtoupper($lesson->getTitle()));
                            $purchasedItems[] = $lesson; // Ajoute la leçon achetée à la liste

                            // Ajouter également le rôle de la formation associée
                            $course = $lesson->getCourse();
                            if ($course) {
                                $user->addRole('ROLE_' . strtoupper($course->getTitle()));
                            }
                        }
                    } elseif ($type === 'courses') {
                        $course = $this->entityManager->getRepository(Course::class)->find($id);
                        if ($course) {
                            $user->addRole('ROLE_' . strtoupper($course->getTitle()));
                            $purchasedItems[] = $course; // Ajoute le cours acheté à la liste

                            // Ajouter également les rôles pour toutes les leçons de la formation
                            $lessons = $course->getLessons();
                            foreach ($lessons as $lesson) {
                                $user->addRole('ROLE_' . strtoupper($lesson->getTitle()));
                            }
                        }
                    }
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Rôles ajoutés avec succès !');
        } else {
            $this->addFlash('error', 'Vous devez être connecté pour finaliser l\'achat.');
        }

        return $this->render('checkout/success.html.twig', [
            'purchasedItems' => $purchasedItems, // Passe les éléments achetés au template
        ]);
    }

    #[Route('/add-role', name: 'add_role')]
    public function addRole(SessionInterface $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            $role = 'ROLE_PURCHASER'; // Ou récupère le rôle depuis la requête
            $user->addRole($role);

            // Persist the user entity
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Role added successfully!');
        } else {
            $this->addFlash('error', 'You must be logged in to add a role.');
        }

        return $this->redirectToRoute('checkout_success'); // Redirige vers la page de succès
    }

    #[Route('/checkout/cancel', name: 'checkout_cancel')]
    public function cancel(): Response
    {
        return $this->render('checkout/cancel.html.twig');
    }
}
