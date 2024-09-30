<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

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
    yield TextField::new('theme', 'Thème')->setRequired(false); // Ajoute cette ligne si nécessaire
}

}
