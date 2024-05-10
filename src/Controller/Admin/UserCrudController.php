<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('username', 'Nombre de usuario'),
            EmailField::new('email', 'Correo electrónico'),
            TextField::new('firstName', 'Nombre'),
            TextField::new('lastName', 'Apellido'),
            ImageField::new('photo', 'Foto')->setBasePath('imagenes/usuarios'),
            AssociationField::new('citas', 'Citas'),
            AssociationField::new('mascotas', 'Mascotas')
        ];
    }
}