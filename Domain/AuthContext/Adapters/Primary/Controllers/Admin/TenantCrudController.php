<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Infrastructure\Entities\Tenant;
use Symfony\Polyfill\Uuid\Uuid;

class TenantCrudController extends AbstractCrudController
{



    public static function getEntityFqcn(): string
    {
        return Tenant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle(Crud::PAGE_INDEX, "Liste des clients")
            ->setPageTitle(Crud::PAGE_NEW, "Créer un nouveau client")
            ->setPageTitle(Crud::PAGE_EDIT, "Modifier un client");


        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action){
            return $action->setLabel("Créer un nouveau client");
        });

        return $actions;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if($entityInstance instanceof Tenant){

            $entityInstance->setUuid(\Symfony\Component\Uid\Uuid::v4()->toRfc4122());
            $entityInstance->setCreatedBy($this->getUser()->getUsername());
            $entityInstance->setIsMaster($this->getUser()->getTenant() !== null);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function createEntity(string $entityFqcn)
    {
        $entity = new Tenant();
        $entity->setCreatedAt(new \DateTimeImmutable());
        return $entity;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name' , "Dénomination de l'entreprise"),
            TextField::new('address', "Adresse"),
            TextField::new('city', "Ville"),
            TextField::new('zipCode', "Code Postal"),
        ];
    }



}
