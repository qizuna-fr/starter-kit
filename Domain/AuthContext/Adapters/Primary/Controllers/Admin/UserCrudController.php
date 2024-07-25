<?php

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Infrastructure\Entities\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function array_keys;
use function str_replace;
use function strtolower;
use function ucfirst;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
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
        return $actions->update(
            Crud::PAGE_INDEX,
            Action::NEW,
            fn(Action $action) => $action->setIcon('fa fa-plus')->setLabel("Ajouter un utilisateur")
        );
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if( $entityInstance instanceof User) {
            $entityInstance->setCreatedAt(new \DateTimeImmutable());

        }
    }



    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(TextFilter::new('username' , 'email'));
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
