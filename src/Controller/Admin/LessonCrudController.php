<?php 

// src/Controller/Admin/LessonCrudController.php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField; // Ajoutez cette ligne

class LessonCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('content', 'Contenu');
        yield TextField::new('videoUrl', 'URL de la vidéo');
        yield IntegerField::new('duration', 'Durée (minutes)');
        yield MoneyField::new('price', 'Prix')->setCurrency('EUR');
        
        // Utilisez ImageField pour le champ image
        yield ImageField::new('image', 'Image')
            ->setBasePath('images/') // Chemin d'accès pour afficher l'image
            ->setUploadDir('public/images/') // Répertoire de téléchargement
            ->setRequired(false)
            ->setHelp('Téléchargez une image pour la leçon.');

        yield AssociationField::new('course', 'Cours')->setRequired(true);
    }
}
