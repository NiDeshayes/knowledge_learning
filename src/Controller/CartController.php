<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
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
        
        $lessonRepository = $this->entityManager->getRepository(Lesson::class);
        $courseRepository = $this->entityManager->getRepository(Course::class);

        $cartItems = [];
        $total = 0;

        foreach ($cart as $type => $items) {
            if ($type === 'lessons') {
                foreach ($items as $id => $item) {
                    $lesson = $lessonRepository->find($id);
                    if ($lesson) {
                        $cartItems[] = [
                            'type' => 'lesson',
                            'item' => $lesson,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ];
                        $total += $item['price'] * $item['quantity'];
                    }
                }
            } elseif ($type === 'courses') {
                foreach ($items as $id => $item) {
                    $course = $courseRepository->find($id);
                    if ($course) {
                        $cartItems[] = [
                            'type' => 'course',
                            'item' => $course,
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                        ];
                        $total += $item['price'] * $item['quantity'];
                    }
                }
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
        $cart = $session->get('cart', []);
        
        if (!isset($cart['courses'][$id])) {
            $cart['courses'][$id] = ['quantity' => 1, 'price' => 100]; // Placeholder pour le prix
        } else {
            $cart['courses'][$id]['quantity'] += 1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/add/lesson/{id}', name: 'app_add_lesson_to_cart')]
    public function addLessonToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (!isset($cart['lessons'][$id])) {
            $cart['lessons'][$id] = ['quantity' => 1, 'price' => 50]; // Placeholder pour le prix
        } else {
            $cart['lessons'][$id]['quantity'] += 1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{type}/{id}', name: 'cart_remove')]
    public function remove(string $type, int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (isset($cart[$type][$id])) {
            unset($cart[$type][$id]);
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('cart');
    }
}
