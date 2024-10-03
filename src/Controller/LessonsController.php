<?php // src/Controller/LessonsController.php

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

        // Vérifiez le rôle de l'utilisateur
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('lessons/admin.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_Guitare')) {
            return $this->render('lessons/Guitare.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_PIANO')) {
            return $this->render('lessons/Piano.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_Css')) {
            return $this->render('lessons/Css.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_Http')) {
            return $this->render('lessons/Http.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_Cuisine')) {
            return $this->render('lessons/Cuisine.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_Jardinage')) {
            return $this->render('lessons/Jardinage.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        // Si l'utilisateur n'a aucun rôle correspondant, redirigez vers une page par défaut
        return $this->redirectToRoute('app_default');
    }

    #[Route('/lessons/lesson1', name: 'app_lesson1')]
    public function lesson1(): Response
    {
        // Logique pour afficher la leçon 1
        return $this->render('lessons/Découverte_de_linstrument.html.twig');
    }

    #[Route('/lessons/lesson2', name: 'app_lesson2')]
    public function lesson2(): Response
    {
        // Logique pour afficher la leçon 2
        return $this->render('lessons/Cursus_de_guitare.html.twig');
    }

    
   


    

}
