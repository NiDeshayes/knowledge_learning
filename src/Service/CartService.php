<?php 

// src/Service/CartService.php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Course;
use App\Entity\Lesson;

class CartService
{
    private SessionInterface $session;
    private array $cart;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->cart = $this->session->get('cart', []);
    }

    public function addCourseToCart(Course $course, int $quantity = 1): void
    {
        if (isset($this->cart['courses'][$course->getId()])) {
            $this->cart['courses'][$course->getId()]['quantity'] += $quantity;
        } else {
            $this->cart['courses'][$course->getId()] = [
                'product' => $course,
                'quantity' => $quantity
            ];
        }
        
        $this->session->set('cart', $this->cart);
    }

    public function addLessonToCart(Lesson $lesson, int $quantity = 1): void
    {
        if (isset($this->cart['lessons'][$lesson->getId()])) {
            $this->cart['lessons'][$lesson->getId()]['quantity'] += $quantity;
        } else {
            $this->cart['lessons'][$lesson->getId()] = [
                'product' => $lesson,
                'quantity' => $quantity
            ];
        }
        
        $this->session->set('cart', $this->cart);
    }

    public function getCartItems(): array
    {
        return $this->cart;
    }

    public function getCartTotal(): float
    {
        $total = 0;

        foreach ($this->cart['courses'] ?? [] as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        foreach ($this->cart['lessons'] ?? [] as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->session->remove('cart');
    }
}
