<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\BusinessLogic\UseCases;


use Domain\SearchEngineContext\Write\Adapters\Secondary\FakeSearchEngine;
use Domain\SearchEngineContext\Write\BusinessLogic\Gateways\SearchEngineGateway;

class UpdateObjectInSearchEngine
{
    public function __construct(
        private SearchEngineGateway $engine
    )
    {
    }

    public function __invoke(string $id, string $indexName, string $jsonData)
    {
        $this->engine->setIndexName($indexName);
        $this->engine->update($id, $jsonData);
    }
}
