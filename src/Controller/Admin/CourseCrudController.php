<?php 


// src/Controller/Admin/CourseCrudController.php

namespace App\Controller\Admin;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField; // Ajoutez cette ligne

class CourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->setRequired(false);
        yield MoneyField::new('price', 'Prix')
            ->setCurrency('EUR')
            ->setRequired(true);
        yield TextField::new('theme', 'Thème')->setRequired(false);
        
        // Utilisez ImageField pour le champ image
        yield ImageField::new('image', 'Image')
            ->setBasePath('images') // Chemin d'accès pour afficher l'image
            ->setUploadDir('public/images') // Répertoire de téléchargement
            ->setRequired(false)
            ->setHelp('Téléchargez une image pour le cours.');
    }
}
