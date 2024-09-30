<?php

namespace App\Controller;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        // Récupérer les leçons à partir du repository
        $lessonRepository = $this->entityManager->getRepository(Lesson::class);
        $cartItems = [];
        $total = 0;

        // Parcourir le panier et récupérer les leçons correspondantes
        foreach ($cart as $id => $quantity) {
            $lesson = $lessonRepository->find($id);
            if ($lesson) {
                $cartItems[] = [
                    'lesson' => $lesson,
                    'quantity' => $quantity,
                ];
                $total += $lesson->getPrice() * $quantity; // Calculer le total
            }
        }

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/course/{id}', name: 'app_add_course_to_cart')]
    public function addCourseToCart(int $id, SessionInterface $session): Response
    {
        // Récupérer le panier existant ou créer un tableau vide
        $cart = $session->get('cart', []);
        
        // Ajoutez le cours avec une quantité de 1
        $cart[$id] = 1;

        // Mettez à jour le panier dans la session
        $session->set('cart', $cart);

        // Rediriger vers la page de panier
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/add/lesson/{id}', name: 'app_add_lesson_to_cart')]
    public function addLessonToCart(int $id, SessionInterface $session): Response
    {
        // Récupérer le panier existant ou créer un tableau vide
        $cart = $session->get('cart', []);
        
        // Vérifiez si l'ID de la leçon est déjà dans le panier
        if (isset($cart[$id])) {
            // Si oui, incrémentez la quantité
            $cart[$id] += 1;
        } else {
            // Sinon, ajoutez-le avec une quantité de 1
            $cart[$id] = 1;
        }

        // Mettez à jour le panier dans la session
        $session->set('cart', $cart);

        // Rediriger vers la page de panier
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]); // Supprimer l'élément du panier
            $session->set('cart', $cart); // Mettre à jour le panier dans la session
        }

        // Rediriger vers la page de panier
        return $this->redirectToRoute('cart');
    }
}
