<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class CartController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private StripeService $stripeService;

    public function __construct(EntityManagerInterface $entityManager, StripeService $stripeService)
    {
        $this->entityManager = $entityManager;
        $this->stripeService = $stripeService;
    }

    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        $lessonRepository = $this->entityManager->getRepository(Lesson::class);
        $courseRepository = $this->entityManager->getRepository(Course::class);

        $cartItems = [];
        $total = 0;

        // Parcours des éléments du panier
        foreach ($cart as $type => $items) {
            foreach ($items as $id => $item) {
                if ($type === 'lessons') {
                    $lesson = $lessonRepository->find($id);
                    if ($lesson) {
                        $cartItems[] = [
                            'type' => 'lesson',
                            'item' => $lesson,
                            'quantity' => $item['quantity'] ?? 1,
                            'price' => $lesson->getPrice(), // Assurez-vous que cette méthode existe
                        ];
                        $total += $lesson->getPrice() * ($item['quantity'] ?? 1);
                    }
                } elseif ($type === 'courses') {
                    $course = $courseRepository->find($id);
                    if ($course) {
                        $cartItems[] = [
                            'type' => 'course',
                            'item' => $course,
                            'quantity' => $item['quantity'] ?? 1,
                            'price' => $course->getPrice(), // Assurez-vous que cette méthode existe
                        ];
                        $total += $course->getPrice() * ($item['quantity'] ?? 1);
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
        $course = $this->entityManager->getRepository(Course::class)->find($id);
        
        if ($course) {
            if (!isset($cart['courses'][$id])) {
                $cart['courses'][$id] = [
                    'quantity' => 1,
                    'price' => $course->getPrice(), // Utilisez le prix ici
                ];
            } else {
                $cart['courses'][$id]['quantity'] += 1;
            }

            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/add/lesson/{id}', name: 'app_add_lesson_to_cart')]
    public function addLessonToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
        
        if ($lesson) {
            if (!isset($cart['lessons'][$id])) {
                $cart['lessons'][$id] = [
                    'quantity' => 1,
                    'price' => $lesson->getPrice(), // Utilisez le prix ici
                ];
            } else {
                $cart['lessons'][$id]['quantity'] += 1;
            }

            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{type}/{id}', name: 'cart_remove')]
    public function remove(string $type, int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (isset($cart[$type][$id])) {
            unset($cart[$type][$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $lineItems = $this->stripeService->createLineItemsFromCart($cart); // Récupère les éléments du panier

        // Créez une session de paiement avec Stripe
        $checkoutSession = $this->stripeService->createCheckoutSession(
            $lineItems,
            $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // Redirigez vers l'URL de la session de paiement
        return $this->redirect($checkoutSession->url);
    }
}
