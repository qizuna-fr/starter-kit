<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Doctrine\ORM\EntityManagerInterface;
use Domain\AuthContext\Adapters\Primary\Controllers\Admin\User\TenantUserController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Infrastructure\Entities\Tenant;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_LOGICIEL_ADMINISTRATEUR')]
class TenantCrudController extends AbstractCrudController
{


    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

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
        $seeTenantUsers = Action::new('seeTenantUsers', 'Voir les utilisateurs', 'fa fa-users')
            ->linkToCrudAction('seeTenantUsersAction');

        $actions->add(Crud::PAGE_INDEX, $seeTenantUsers);

        $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setLabel("Créer un nouveau client")->setIcon("fa fa-plus");
        });

        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setLabel("Modifier")->setIcon("fa fa-edit");
        });

        $actions->remove(Crud::PAGE_INDEX, Action::DELETE);

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

            TextField::new('name', "Dénomination de l'entreprise"),
            FormField::addRow(),

            FormField::addFieldset('Informations de contact')
                ->setIcon('phone')->addCssClass('optional'),
             //   ->setHelp('Phone number is preferred'),
            TextField::new('address', "Adresse")->setColumns(12),
            //TextField::new('address2', "Adresse"),
            TextField::new('city', "Ville")->setColumns(6),
            TextField::new('zipCode', "Code Postal")->setColumns(6),
        ];
    }

    public function seeTenantUsersAction(AdminContext $context)
    {
        $tenant = $context->getEntity()->getInstance();
        $tenantId = $tenant->getId();

        $url = $this->adminUrlGenerator
            ->setController(TenantUserController::class)
            ->setAction(Action::INDEX)
            ->set('tenantId' , $tenant->getId())
            ->setEntityId(null)
            ->generateUrl();

        return $this->redirect($url);
    }
}
