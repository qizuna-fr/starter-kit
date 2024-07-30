<?php

namespace Domain\AuthContext\Adapters\Primary\Controllers\Admin;


use Domain\AuthContext\Adapters\Primary\Controllers\Admin\User\TenantUserController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Infrastructure\Entities\Tenant;
use Infrastructure\Entities\User;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


#[IsGranted('ROLE_LOGICIEL_ADMINISTRATEUR')]
class ManagerDashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $urlGenerator, private ChartBuilderInterface $chartBuilder)
    {
    }

    public function configureAssets(): Assets
    {
        $assets = Assets::new();
        $assets->addWebpackEncoreEntry('app');
        //$assets->addJsFile('build/app.js');
        $assets->addCssFile('build/admin/admin.css');
        return $assets;
    }

    #[Route('/admin/manager', name: 'app_admin_manager')]
    public function index(): Response
    {
        $chartNewCustomersLastMonth = $this->chartBuilder->createChart(Chart::TYPE_PIE);
        $chartNewCustomersLastMonth->setData(
            [
                'labels' => ['< 1 jour', '1-7 jours', '8-30 jours', '> 30 jours'],
                'datasets' => [
                    [
                        'label' => 'Nombre de tickets',
                        'backgroundColor' => ['rgb(240,78,35)', 'rgb(51,226,209)', 'RGB(32 42 55)', 'RGB(228 232 237)'],
                        'data' => [89, 15, 3, 1],
                    ],
                ],
            ]
        );

        $chartNewCustomersLastMonth->setOptions(
            [
                'maintainAspectRatio' => false,
            ]
        );

        $chartBar = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chartBar->setData(
            [
                'labels' => [
                    'Janvier',
                    'Février',
                    'Mars',
                    'Avril',
                    'Mai',
                    'Juin',
                    'Juillet',
                    'Août',
                    'Septembre',
                    'Octobre',
                    'Novembre',
                    'Décembre',
                ],
                'datasets' => [
                    [
                        'label' => "Tickets ouverts niveau 1",
                        'backgroundColor' => 'rgb(240,78,35)',
                        'borderColor' => 'RGB(194 61 25)',
                        'data' => [12, 10, 5, 2, 20, 30, 45, 48, 50, 55, 60, 70],
                    ],
                    [
                        'label' => "Tickets ouverts niveau 2",
                        'backgroundColor' => 'RGB(51 226 209)',
                        'borderColor' => 'RGB(37 189 173)',
                        'data' => [1, 0, 0, 0, 2, 3, 4, 5, 6, 7, 8, 12],
                    ],
                ],
            ]
        );

        $chartBar->setOptions(
            [
                'maintainAspectRatio' => false,
            ]
        );
        return $this->render(
            'bundles/EasyAdminBundle/welcome.html.twig',
            [
                'chart' => $chartBar,
                'chartNewCustomersLastMonth' => $chartNewCustomersLastMonth,
            ]
        );
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

    public function configureMenuItems(): iterable
    {
        $user = $this->getUser();

        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-dashboard')
            ->setPermission('ROLE_CLIENT_ADMINISTRATEUR');
        //yield MenuItem::linkToCrud('Clients', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Clients', 'fa fa-address-book', Tenant::class)
            ->setController(TenantCrudController::class)
            ->setPermission('ROLE_LOGICIEL_ADMINISTRATEUR');;

        // si l'utilisateur connecté est attaché à un client
        if ($user->getTenant() !== null) {
            yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class)
                ->setController(TenantUserController::class)
                ->setPermission('ROLE_CLIENT_ADMINISTRATEUR')
                ->setQueryParameter('tenantId', $user->getTenant()->getId());
        }

        yield MenuItem::linkToCrud(
            'Administrateurs',
            'fa fa-tags',
            User::class,
        )
            ->setController(InternalUserCrudController::class)
            ->setPermission('ROLE_LOGICIEL_ADMINISTRATEUR');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
