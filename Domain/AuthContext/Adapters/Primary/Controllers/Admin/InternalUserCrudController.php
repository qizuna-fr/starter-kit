<?php

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Doctrine\ORM\QueryBuilder;
use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function array_keys;
use function bin2hex;
use function random_bytes;
use function str_replace;
use function strtolower;
use function ucfirst;

#[IsGranted('ROLE_ADMIN')]
class InternalUserCrudController extends AbstractCrudController
{


    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private UserRepository $userRepository
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, "Utilisateurs internes de l'application")
            ->setPageTitle(Crud::PAGE_EDIT, "Modifier un utilisateur interne")
            ->setPageTitle(Crud::PAGE_NEW, "Créer un utilisateur interne");
            //->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        $formattedRoles = $this->getFormattedRoles();

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
                ->setChoices($formattedRoles)
                ->allowMultipleChoices()
                ->renderExpanded(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $deactivateUser = Action::new('deactivate', 'Désactiver l\'utilisateur')
            ->linkToCrudAction('deactivateUser')
            ->setCssClass('btn btn-danger')
            ->displayIf(fn($entity) => $entity->getActivatedAt() !== null);

        $resendActivationLink = Action::new('sendActivationLink', 'Envoyer lien d\'activation')
            ->linkToCrudAction('sendActivationLink')
            ->setCssClass('btn btn-success')
            ->setIcon('fa fa-envelope')
            ->displayIf(fn($entity) => $entity->getActivatedAt() === null);

        $actions->update(
            Crud::PAGE_INDEX,
            Action::NEW,
            fn(Action $action) => $action->setIcon('fa fa-plus')->setLabel("Ajouter un utilisateur")
        );



        $actions->add(Crud::PAGE_EDIT, $deactivateUser);
        $actions->add(Crud::PAGE_EDIT, $resendActivationLink);

        return $actions;
    }

    public function deactivateUser(AdminContext $context): Response
    {
        $user = $context->getEntity()->getInstance();
        $user->setActivatedAt(null);

        $this->userRepository->save($user, true);

        return $this->redirect(
            $this->adminUrlGenerator->setController(InternalUserCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

    public function sendActivationLink(AdminContext $adminContext): Response
    {

        $user = $adminContext->getEntity()->getInstance();



    }

    public function createEntity(string $entityFqcn)
    {
        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setActivationToken(bin2hex(random_bytes(32)));
        return $user;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $qb = parent::createIndexQueryBuilder(
            $searchDto,
            $entityDto,
            $fields,
            $filters
        );

        return $qb->andWhere('entity.tenant IS NULL');
    }

    private function getFormattedRoles(): array
    {
        $roles = array_keys($this->getParameter('security.role_hierarchy.roles'));

        $formattedRoles = [];
        foreach ($roles as $role) {
            $role_string = $role;
            $role = str_replace("ROLE_", "", $role);
            $role = str_replace("_", " ", $role);
            $role = strtolower($role);
            $role = ucfirst($role);

            $formattedRoles[$role] = $role_string;
        }
        return $formattedRoles;
    }
}
