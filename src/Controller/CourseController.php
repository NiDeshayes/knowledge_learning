<?php 

// src/Controller/CourseController.php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Service\CartService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Importer SessionInterface
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CourseController extends AbstractController
{
    private CourseRepository $courseRepository;
    private StripeService $stripeService;
    private CartService $cartService;

    public function __construct(
        CourseRepository $courseRepository, 
        StripeService $stripeService, 
        CartService $cartService,
        SessionInterface $session // Injection de SessionInterface
    ) {
        $this->courseRepository = $courseRepository;
        $this->stripeService = $stripeService;
        $this->cartService = $cartService;
        $this->session = $session; // Initialisation de la session
    }

    #[Route('/courses', name: 'app_courses', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $searchTerm = $request->query->get('search', '');

        // Si un terme de recherche est présent, filtrer les résultats
        if ($searchTerm) {
            $courses = $this->courseRepository->findBySearchTerm($searchTerm);
        } else {
            $courses = $this->courseRepository->findAll();
        }

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/courses/{id}', name: 'app_course_detail', methods: ['GET'])]
    public function detail(int $id): Response
    {
        $course = $this->courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/courses/{id}/add-to-cart', name: 'app_add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request, int $id): Response
    {
        $course = $this->courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $lessonId = $request->request->get('lesson_id');
        $packageType = $request->request->get('package');

        if ($packageType === 'full') {
            // Ajouter tout le cours au panier
            $this->cartService->addCourseToCart($course);
        } elseif ($lessonId) {
            // Ajouter une leçon spécifique
            $lesson = $course->getLessons()->filter(function ($lesson) use ($lessonId) {
                return $lesson->getId() === $lessonId;
            })->first();

            if ($lesson) {
                $this->cartService->addLessonToCart($lesson);
            }
        }

        return $this->redirectToRoute('app_course_detail', ['id' => $id]);
    }

    #[Route('/checkout', name: 'app_checkout', methods: ['GET'])]
    public function checkout(): Response
    {
        $cartItems = $this->cartService->getCartItems();
        $lineItems = $this->stripeService->createLineItemsFromCart($cartItems);
        
        // Ajouter les URLs de succès et d'annulation
        $successUrl = $this->generateUrl('app_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('app_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Créer la session de paiement
        $session = $this->stripeService->createCheckoutSession($lineItems, $successUrl, $cancelUrl);

        // Rediriger vers Stripe
        return $this->redirect($session->url);
    }
}
