<?php

namespace App;

use App\DoctrineType\EnumType;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function is_file;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $configDir = $this->getConfigDir();

        $container->import($configDir.'/{packages}/*.{php,yaml}');
        $container->import($configDir.'/{packages}/'.$this->environment.'/*.{php,yaml}');

        if (is_file($configDir.'/services.yaml')) {
            $container->import($configDir.'/services.yaml');
            $container->import($configDir.'/{services}_'.$this->environment.'.yaml');
        } else {
            $container->import($configDir.'/{services}.php');
            $container->import($configDir.'/{services}_'.$this->environment.'.php');
        }

        // Vérification du sous-domaine
        if ($this->getEnvironment() === 'prod' && ($this->isDevSubdomain() || $this->isLocalhost())) {
            $container->import('../config/packages/mailer_dev_subdomain.yaml');
        }
    }

    private function isDevSubdomain(): bool
    {
        // Récupère le sous-domaine actuel
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return preg_match('/\.dev\.qizuna\.fr$/', $host) === 1;
    }

    private function isLocalhost(): bool
    {
        // Récupère l'adresse IP du serveur
        $serverIp = $_SERVER['SERVER_ADDR'] ?? '';

        // Vérifie si l'IP est 127.0.0.1
        return $serverIp === '127.0.0.1';
    }
}
