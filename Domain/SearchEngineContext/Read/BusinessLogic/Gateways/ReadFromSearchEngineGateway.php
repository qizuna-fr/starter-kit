<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\BusinessLogic\Gateways;

interface ReadFromSearchEngineGateway
{
    public function executeQuery(string $query): ?array;


}
