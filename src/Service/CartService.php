<?php 

namespace App\Service;

use App\Entity\Course;
use App\Entity\Lesson;

class CartService
{
    private array $cart = [];

    public function addCourseToCart(Course $course): void
    {
        $this->cart[$course->getId()] = ['type' => 'course', 'quantity' => 1];
    }

    public function addLessonToCart(Lesson $lesson): void
    {
        $this->cart[$lesson->getId()] = ['type' => 'lesson', 'quantity' => 1];
    }

    public function getCart(): array
    {
        return $this->cart;
    }

    // Ajoutez d'autres méthodes comme remove, etc., si nécessaire
}
