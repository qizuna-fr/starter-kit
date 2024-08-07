<?php

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Domain\AuthContext\Adapters\Secondary\Repositories\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Infrastructure\Entities\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function array_keys;
use function str_replace;
use function strtolower;
use function ucfirst;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{


    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private UserRepository $userRepository
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, "Utilisateurs de l'application")
            ->setPageTitle(Crud::PAGE_EDIT, "Modifier un utilisateur")
            ->setPageTitle(Crud::PAGE_NEW, "Créer un nouvel utilisateur");

    }

    public function configureFields(string $pageName): iterable
    {
        $formattedRoles = $this->getFormattedRoles();

        yield IdField::new('id', "Numéro")->onlyOnIndex();
        yield AssociationField::new('tenant', "Entreprise");
        yield TextField::new('username', "Nom d'utilisateur");
        yield TextField::new('email', "Adresse email");
        yield BooleanField::new('isActive', "Compte activé")->onlyOnIndex();
        yield DateField::new('activated_at', "Date d'activation du compte")->onlyOnForms();
        yield ChoiceField::new('roles', "Rôles de l'utilisateur")
            ->renderAsBadges()
            ->setChoices($formattedRoles)
            ->allowMultipleChoices()
            ->renderExpanded();
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
        //$user->setDeactivatedAt(new \DateTimeImmutable());

        $this->userRepository->save($user, true);

        return $this->redirect(
            $this->adminUrlGenerator->setController(UserCrudController::class)->generateUrl()
        );
    }

    public function sendActivationLink(AdminContext $adminContext): Response
    {
        dd($adminContext);
    }

    public function createEntity(string $entityFqcn)
    {
        $user = new User();
        $user->setCreatedAt(new \DateTimeImmutable());
        return $user;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(TextFilter::new('username' , 'email'));
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

        $tenantId = $this->getContext()->getRequest()->query->get('tenantId');
        if($tenantId !== null){
            $qb->andWhere('entity.tenant = :tenantId')
                ->setParameter('tenantId', $tenantId);

            return $qb;
        }

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
