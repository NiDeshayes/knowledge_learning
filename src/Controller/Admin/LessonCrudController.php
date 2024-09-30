<?php 

// src/Controller/Admin/LessonCrudController.php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class LessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre'); // Utilise le bon champ ici
        yield TextareaField::new('content', 'Contenu');
        yield TextField::new('videoUrl', 'URL de la vidéo'); // Assure-toi d'ajouter ce champ
        yield AssociationField::new('course', 'Cours')->setRequired(true); // Champ pour la relation avec Course
    }
}
