<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/


namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin\User;


use Doctrine\ORM\QueryBuilder;
use Domain\AuthContext\Adapters\Secondary\Repositories\TenantRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Infrastructure\Entities\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function array_keys;
use function bin2hex;
use function func_get_args;
use function random_bytes;
use function sprintf;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function ucfirst;

#[IsGranted('ROLE_CLIENT_ADMINISTRATEUR')]
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

    public function configureCrud(Crud $crud): Crud
    {
        $tenant = $this->requestStack->getCurrentRequest()->query->get('tenantId');

        if ($tenant === null) {
            return $crud;
        }

        $tenant = $this->tenantRepository->find($tenant);

        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, sprintf("Liste des employés de % s", $tenant->getName()))
            ->setPageTitle(Crud::PAGE_EDIT, "Modifier un utilisateur")
            ->setPageTitle(Crud::PAGE_NEW, "Créer un utilisateur");
        //->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
//        yield IdField::new('id', "Numéro")->onlyOnIndex();
//        yield TextField::new('fullname', "Nom complet")->onlyOnIndex();
//        yield TextField::new('username', "Nom d'utilisateur");
//        yield TextField::new('email', "Adresse E-mail");
//        yield TextField::new('firstname', "Prénom")->hideOnIndex();
//        yield TextField::new('lastname', "Nom")->hideOnIndex();

        return [
            IdField::new('id', "Numéro")->onlyOnIndex(),
            //yield AssociationField::new('tenant', "Entreprise");
            FormField::addFieldset('Informations personnelles'),

            TextField::new('firstname', "Prénom")->setColumns(4)->onlyOnForms(),
            TextField::new('lastname', "Nom")->setColumns(4)->onlyOnForms(),
            TextField::new('email', "Adresse email")->setColumns(4),
            TextField::new('fullname', "Nom")->onlyOnIndex(),

            FormField::addFieldset('Informations de connexion et rôles'),
            TextField::new('username', "Nom d'utilisateur"),

            BooleanField::new('isActive', "Compte activé")->onlyOnIndex(),
            //yield BooleanField::new('isActive', "Compte activé")->onlyOnIndex();
            //yield DateField::new('activated_at', "Date d'activation du compte");
            ChoiceField::new('roles', "Rôles de l'utilisateur")
                ->renderAsBadges()
                ->setChoices($this->getFormattedRoles())
                ->allowMultipleChoices()
                ->renderExpanded(),
        ];
    }


    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
                ->setLabel("Modifier")
                ->setIcon("fa fa-edit")
                ->linkToUrl(function ($entity) {
                    return $this->adminUrlGenerator
                        ->setController(TenantUserController::class)
                        ->setAction(Action::EDIT)
                        ->setEntityId($entity->getId())
                        ->generateUrl();
                });
        });

        $newAction = Action::new('new', 'Créer un nouvel employé')
            ->setLabel('Ajouter un utilisateur')
            ->createAsGlobalAction()
            ->linkToUrl(function () {
                return $this->adminUrlGenerator
                    ->setController(self::class)
                    ->setAction(Action::NEW)
                    ->generateUrl();
            })
            ->setIcon('fa fa-plus');


        $impersonateAction = Action::new('impersonate', 'Se connecter en tant que')
            ->linkToCrudAction('impersonate')
            ->setIcon('fa fa-user')
            ->displayIf(fn($entity) => $entity->getActivatedAt() !== null);


        $actions->add(Crud::PAGE_INDEX, $impersonateAction);
        $actions->remove(Crud::PAGE_INDEX, Action::NEW);
        $actions->add(Crud::PAGE_INDEX, $newAction);
        $actions->remove(Crud::PAGE_INDEX, Action::DELETE);

        return $actions;
    }

    public function impersonate(AdminContext $context)
    {
        $user = $context->getEntity()->getInstance();
        $url = $this->generateUrl('app_index', ['_switch_user' => $user->getUsername()]);
        return $this->redirect($url);
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
        $request = $this->requestStack->getCurrentRequest();
        $tenantId = $request->query->get('tenantId');

        $tenant = $this->tenantRepository->find($tenantId);

        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setTenant($tenant);
        $user->setActivationToken(bin2hex(random_bytes(32)));
        return $user;
    }

    private function getFormattedRoles(): array
    {
        $roles = array_keys($this->getParameter('security.role_hierarchy.roles'));

        $formattedRoles = [];
        foreach ($roles as $role) {
            $role_string = $role;

            //ignore les roles
            if (str_starts_with($role, "ROLE_CLIENT")) {
                $role = str_replace("ROLE_", "", $role);
                $role = str_replace("_", " ", $role);
                $role = strtolower($role);
                $role = ucfirst($role);
                $formattedRoles[$role] = $role_string;
            }
        }
        return $formattedRoles;
    }


}
