<?php

namespace App\Controller\Admin;

use App\Entity\Citas;
use App\Event\CitaCanceladaEvent;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CitasCrudController extends AbstractCrudController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

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

    public function deleteEntity($entityManager, $entityInstance): void
    {
        parent::deleteEntity($entityManager, $entityInstance);

        $event = new CitaCanceladaEvent($entityInstance);
        $this->eventDispatcher->dispatch($event, CitaCanceladaEvent::NAME);
    }
}