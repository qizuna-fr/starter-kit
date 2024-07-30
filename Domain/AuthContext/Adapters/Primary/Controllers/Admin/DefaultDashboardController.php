<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Infrastructure\Entities\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultDashboardController extends AbstractDashboardController
{


    public function __construct(private Security $security)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        //$user = $context->getUser();

        if ($this->security->isGranted('ROLE_LOGICIEL_ADMINISTRATEUR')) {
            return $this->redirectToRoute('app_admin_manager');
        }

        if ($this->security->isGranted('ROLE_CLIENT_ADMINISTRATEUR')) {
            return $this->redirectToRoute('app_admin_client');
        }

        throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette section.');

    }
}
