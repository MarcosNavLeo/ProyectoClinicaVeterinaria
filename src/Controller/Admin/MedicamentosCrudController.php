<?php

namespace App\Controller\Admin;

use App\Entity\Medicamentos;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;

class MedicamentosCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Medicamentos::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nombre', 'Nombre del medicamento'),
            TextField::new('instrucciones', 'Instrucciones del medicamento'),
            TextField::new('dosis', 'dosis del medicamento'),
            AssociationField::new('tratamientos', 'Tratamientos')
        ];
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Tratamientos');
    }
}