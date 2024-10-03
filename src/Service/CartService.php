<?php 


// src/Service/CartService.php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Course;
use App\Entity\Lesson;

class CartService
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addCourseToCart(Course $course, int $quantity = 1): void
    {
        // ... votre logique ici
    }

    public function addLessonToCart(Lesson $lesson, int $quantity = 1): void
    {
        // ... votre logique ici
    }

    public function getCartItems(): array
    {
        return $this->session->get('cart', []);
    }

    public function getCartTotal(): float
    {
        $total = 0;

        foreach ($this->getCartItems()['courses'] ?? [] as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        foreach ($this->getCartItems()['lessons'] ?? [] as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    }

    public function clearCart(): void
    {
        $this->session->remove('cart');
    }
}
