<?php

namespace App\Controller\Admin;

use App\Entity\Citas;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CitasCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Citas::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            DateTimeField::new('fecha'),
            DateTimeField::new('hora'),
            TextField::new('motivo'),
            AssociationField::new('user'),
            AssociationField::new('mascotas'),
            AssociationField::new('consultas')
        ];
    }
}