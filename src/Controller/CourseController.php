<?php 

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Service\CartService;
use App\Service\StripeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CourseController extends AbstractController
{
    private CourseRepository $courseRepository;
    private StripeService $stripeService;
    private CartService $cartService;

    public function __construct(
        CourseRepository $courseRepository, 
        StripeService $stripeService, 
        CartService $cartService
    ) {
        $this->courseRepository = $courseRepository;
        $this->stripeService = $stripeService;
        $this->cartService = $cartService;
    }

    #[Route('/courses', name: 'app_courses', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $cart = $this->cartService->getCartItems();

        // Récupérer le terme de recherche depuis la requête
        $searchTerm = $request->query->get('search', '');

        // Filtrer les résultats selon le terme de recherche s'il existe
        if ($searchTerm) {
            $courses = $this->courseRepository->findBySearchTerm($searchTerm);
        } else {
            $courses = $this->courseRepository->findAll();
        }

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
            'cart' => $cart,
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

        // Récupérer les informations envoyées via le formulaire
        $lessonId = $request->request->get('lesson_id');
        $packageType = $request->request->get('package');

        if ($packageType === 'full') {
            // Ajouter tout le cours au panier
            $this->cartService->addCourseToCart($course);
            $this->addFlash('success', 'The full course has been added to the cart.');
        } elseif ($lessonId) {
            // Rechercher la leçon par son ID
            $lesson = $course->getLessons()->filter(function ($lesson) use ($lessonId) {
                return $lesson->getId() === (int)$lessonId;
            })->first();

            if ($lesson) {
                $this->cartService->addLessonToCart($lesson);
                $this->addFlash('success', 'The lesson has been added to the cart.');
            } else {
                $this->addFlash('error', 'Lesson not found.');
            }
        }

        // Rediriger vers la page de détail du cours
        return $this->redirectToRoute('app_course_detail', ['id' => $id]);
    }

    #[Route('/checkout', name: 'app_checkout', methods: ['GET'])]
    public function checkout(): Response
    {
        $cartItems = $this->cartService->getCartItems();
        $lineItems = $this->stripeService->createLineItemsFromCart($cartItems);
        
        // URLs de succès et d'annulation
        $successUrl = $this->generateUrl('app_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->generateUrl('app_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Créer la session de paiement Stripe
        $session = $this->stripeService->createCheckoutSession($lineItems, $successUrl, $cancelUrl);

        // Rediriger vers Stripe pour le paiement
        return $this->redirect($session->url);
    }
}
