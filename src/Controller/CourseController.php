<?php 

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Service\CartService; // Assurez-vous d'importer votre service de panier
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Ajoutez ceci

class CourseController extends AbstractController
{
    private CourseRepository $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    #[Route('/courses', name: 'app_courses')]
    public function index(Request $request): Response
    {
        $searchTerm = $request->query->get('search', '');
        $courses = $this->courseRepository->findBySearchTerm($searchTerm);

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/courses/{id}', name: 'app_course_detail')]
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
    public function addToCart(Request $request, int $id, SessionInterface $session): Response // Passer la session ici
    {
        // Récupération du cursus
        $course = $this->courseRepository->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        // Récupération des informations du formulaire
        $lessonId = $request->request->get('lesson_id');
        $packageType = $request->request->get('package');

        $cartService = new CartService(); // Instancier ici le service

        if ($packageType === 'full') {
            // Ajouter le cursus complet au panier
            $cartService->addCourseToCart($course);
        } elseif ($lessonId) {
            // Ajouter la leçon spécifique au panier
            $lesson = $course->getLessons()->filter(function ($lesson) use ($lessonId) {
                return $lesson->getId() === $lessonId;
            })->first();

            if ($lesson) {
                $cartService->addLessonToCart($lesson);
            }
        }

        // Rediriger vers la page de détail ou de cours
        return $this->redirectToRoute('app_course_detail', ['id' => $id]);
    }
}
