<?php

namespace App\Controller;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/lessons', name: 'app_lessons')]
    public function index(): Response
    {
        // Récupérer toutes les leçons
        $lessonRepository = $this->entityManager->getRepository(Lesson::class);
        $lessons = $lessonRepository->findAll();

        return $this->render('lessons/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }
}
