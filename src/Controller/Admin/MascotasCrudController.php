<?php

namespace App\Controller\Admin;

use App\Entity\Mascotas;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class MascotasCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Mascotas::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre'),
            TextField::new('especie'),
            TextField::new('raza'),
            DateTimeField::new('fecha_nacimiento', 'Fecha de Nacimiento'),
            AssociationField::new('user', 'Propietario'),
            AssociationField::new('citas', 'Citas'),
            ImageField::new('foto')->setUploadDir('public/imagenes/mascotas')
        ];
    }
}