<?php

namespace App\Controller\Admin;

use App\Entity\Tratamientos;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class TratamientosCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tratamientos::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('desc_tratamiento', 'Descripción del tratamiento'),
            DateTimeField::new('fecha_tratamiento', 'Fecha del tratamiento'),
            TextField::new('duracion', 'Duración'),
            NumberField::new('costo', 'Costo'),
            AssociationField::new('consultas', 'Consultas'),
            AssociationField::new('medicamentos', 'Medicamentos')
        ];
    }
}