<?php


namespace App\Controller\Admin;

use App\Entity\Consultas;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConsultasCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Consultas::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('fecha_hora', 'Fecha y Hora'),
            TextField::new('diagnostico', 'Diagnóstico'),
            AssociationField::new('citas', 'Citas'),
            AssociationField::new('tratamientos', 'Tratamientos')
        ];
    }
}