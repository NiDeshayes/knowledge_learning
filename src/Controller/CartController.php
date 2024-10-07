<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\User;
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

        foreach ($cart as $type => $items) {
            foreach ($items as $id => $item) {
                if ($type === 'lessons') {
                    $lesson = $lessonRepository->find($id);
                    if ($lesson) {
                        $lessonPrice = $lesson->getPrice(); 
                        $cartItems[] = [
                            'type' => 'lesson',
                            'item' => $lesson,
                            'quantity' => $item['quantity'] ?? 1,
                            'price' => $lessonPrice, // Utilisation du prix divisé
                        ];
                        $total += $lessonPrice * ($item['quantity'] ?? 1); // Total ajusté
                    }
                } elseif ($type === 'courses') {
                    $course = $courseRepository->find($id);
                    if ($course) {
                        $cartItems[] = [
                            'type' => 'course',
                            'item' => $course,
                            'quantity' => $item['quantity'] ?? 1,
                            'price' => $course->getPrice(), // Le prix des cours reste inchangé
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
                    'price' => $course->getPrice(),
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
                    'price' => $lesson->getPrice(),
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
        $lineItems = $this->stripeService->createLineItemsFromCart($cart);

        $checkoutSession = $this->stripeService->createCheckoutSession(
            $lineItems,
            $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        return $this->redirect($checkoutSession->url);
    }

    #[Route('/cart/checkout/success', name: 'checkout_success')]
    public function checkoutSuccess(SessionInterface $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            $cart = $session->get('cart', []);
            
            foreach ($cart as $type => $items) {
                foreach ($items as $id => $item) {
                    if ($type === 'lessons') {
                        $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
                        if ($lesson) {
                            $user->addRole('ROLE_' . strtoupper($lesson->getTitle()));
                        }
                    } elseif ($type === 'courses') {
                        $course = $this->entityManager->getRepository(Course::class)->find($id);
                        if ($course) {
                            $user->addRole('ROLE_' . strtoupper($course->getTitle()));
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

        return $this->render('cart/success.html.twig');
    }

    #[Route('/cart/checkout/cancel', name: 'checkout_cancel')]
    public function checkoutCancel(): Response
    {
        return $this->render('cart/cancel.html.twig');
    }
}
