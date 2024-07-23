<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Service\Security;


use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use function count;
use function dump;
use function get_class;


class TwoFactorSecurityConfig
{
    public function __construct(private TwoFactorProviderRegistry $twoFactorProviderRegistry)
    {
    }

    public function isTwoFactorEnabled(): bool
    {


        return count($this->getProviders())>0;

    }

    public function getProviders(){
        $providers = [];

        // Vérifiez la liste des providers enregistrés
        foreach ($this->twoFactorProviderRegistry->getAllProviders() as $provider) {
            $providers[] = $provider;
        }

        return $providers;
    }
}

