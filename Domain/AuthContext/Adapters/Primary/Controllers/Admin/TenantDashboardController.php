<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Domain\AuthContext\Adapters\Primary\Controllers\Admin\User\TenantUserController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Infrastructure\Entities\Tenant;
use Infrastructure\Entities\User;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CLIENT_ADMINISTRATEUR')]
class TenantDashboardController extends AbstractDashboardController
{
    public function configureAssets(): Assets
    {
        $assets = Assets::new();
        $assets->addWebpackEncoreEntry('app');
        //$assets->addJsFile('build/app.js');
        $assets->addCssFile('build/admin/admin.css');
        return $assets;
    }

    #[Route('/admin/customer', name: 'app_admin_client')]
    public function index(): Response
    {
        return $this->render(
            'bundles/EasyAdminBundle/tenant_welcome.html.twig',
            [
                'user' => 'cuicui'
            ]
        );
    }

    public function configureMenuItems(): iterable
    {
        $user = $this->getUser();

        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-dashboard')
            ->setPermission('ROLE_CLIENT_ADMINISTRATEUR');

        // si l'utilisateur connecté est attaché à un client
        if ($user->getTenant() !== null) {
            yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class)
                ->setController(TenantUserController::class)
                ->setPermission('ROLE_CLIENT_ADMINISTRATEUR')
                ->setQueryParameter('tenantId', $user->getTenant()->getId());
        }
    }

    public function configureDashboard(): Dashboard
    {
        $package = new Package(
            new JsonManifestVersionStrategy(
                Path::join($this->getParameter('kernel.project_dir'), "public/build/manifest.json")
            )
        );

        $image = $package->getUrl("build/images/login_logo.png");
        $favicon = $package->getUrl("build/images/favicon.png");

        return Dashboard::new()
            ->renderContentMaximized()
            ->setTitle('<img src="' . $image . '">')
            ->setFaviconPath($favicon);
    }
}
