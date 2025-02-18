<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Services;


use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\ReadFromSearchEngineGateway;

class CentralizedSearchEngineService
{


    public function __construct(
        private ReadFromSearchEngineGateway $engineGateway
    )
    {
    }

    public function query(string $string)
    {
        return $this->engineGateway->executeQuery($string);

    }
}
