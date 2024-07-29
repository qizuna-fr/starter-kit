<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin\User;


use Doctrine\ORM\QueryBuilder;
use Domain\AuthContext\Adapters\Primary\Controllers\Admin\UserCrudController;
use Domain\AuthContext\Adapters\Secondary\Repositories\TenantRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Infrastructure\Entities\User;
use Symfony\Component\HttpFoundation\RequestStack;

use function sprintf;


class TenantUserController extends AbstractCrudController
{


    public function __construct(
        private RequestStack $requestStack,
        private TenantRepository $tenantRepository,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', "Numéro")->onlyOnIndex();
        yield TextField::new('fullname', "Nom complet")->onlyOnIndex();
        yield TextField::new('username', "Nom d'utilisateur");
        yield TextField::new('email', "Adresse E-mail");
        yield TextField::new('firstname', "Prénom")->hideOnIndex();
        yield TextField::new('lastname', "Nom")->hideOnIndex();
    }

    public function configureCrud(Crud $crud): Crud
    {
        $tenant = $this->requestStack->getCurrentRequest()->query->get('tenantId');

        if($tenant === null) {
            return $crud;
        }

        $tenant = $this->tenantRepository->find($tenant);

        return $crud->setPageTitle(
            Crud::PAGE_INDEX,
            sprintf("Liste des employés de % s", $tenant->getName())
        );
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
                ->setLabel("Modifier")
                ->setIcon("fa fa-edit")
                ->linkToUrl(function ($entity) {
                    return $this->adminUrlGenerator
                        ->setController(UserCrudController::class)
                        ->setAction(Action::EDIT)
                        ->setEntityId($entity->getId())
                        ->generateUrl();
                });
        });

        $actions->update(Crud::PAGE_INDEX,
                         Action::NEW,
            function (Action $action) {
                return $action
                    ->setLabel("Créer un nouvel employé")
                    ->setIcon("fa fa-plus");
            }
        );


        $actions->remove(Crud::PAGE_INDEX, Action::DELETE);

        return $actions;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        //dd($this->requestStack->getCurrentRequest()->query->get('tenantId'));
        $qb = parent::createIndexQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters
        );

        $qb->andWhere('entity.tenant = :tenantId')
            ->setParameter('tenantId', $this->requestStack->getCurrentRequest()->query->get('tenantId'));

        return $qb;
    }

    public function createEntity(string $entityFqcn)
    {
        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setTenant(null);
        return $user;
    }


}
