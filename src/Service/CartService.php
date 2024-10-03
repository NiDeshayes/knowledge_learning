<?php 

// src/Service/CartService.php

namespace App\Service;
use Symfony\Component\HttpFoundation\Session\SessionFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Course;
use App\Entity\Lesson;

class CartService
{
    private SessionInterface $session;
    
     public function __construct(SessionFactory $sessionFactory)
     {
        $this->session = $sessionFactory->createSession();
    }
    
    public function addCourseToCart(Course $course, int $quantity = 1): void
    {
        $cart = $this->getCartItems();
        // Ajoutez ou mettez à jour le cours dans le panier
        $cart['courses'][$course->getId()] = [
            'product' => $course,
            'quantity' => (isset($cart['courses'][$course->getId()]) ? $cart['courses'][$course->getId()]['quantity'] : 0) + $quantity,
        ];
        $this->session->set('cart', $cart); // Enregistrer le panier dans la session
    }

    public function addLessonToCart(Lesson $lesson, int $quantity = 1): void
    {
        $cart = $this->getCartItems();
        // Ajoutez ou mettez à jour la leçon dans le panier
        $cart['lessons'][$lesson->getId()] = [
            'product' => $lesson,
            'quantity' => (isset($cart['lessons'][$lesson->getId()]) ? $cart['lessons'][$lesson->getId()]['quantity'] : 0) + $quantity,
        ];
        $this->session->set('cart', $cart); // Enregistrer le panier dans la session
    }

    public function getCartItems(): array
    {
        return $this->session->get('cart', []); // Récupérer les articles du panier
    }

    public function getCartTotal(): float
    {
        $total = 0;

        // Calculer le total pour les cours
        foreach ($this->getCartItems()['courses'] ?? [] as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        // Calculer le total pour les leçons
        foreach ($this->getCartItems()['lessons'] ?? [] as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total; // Retourner le total
    }

    public function clearCart(): void
    {
        $this->session->remove('cart'); // Effacer le panier
    }
}
