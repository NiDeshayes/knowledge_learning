<?php 

// src/Controller/CourseController.php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    private $courseRepository;

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
}
