<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\BusinessLogic\UseCases;


use Domain\SearchEngineContext\Write\Adapters\Secondary\FakeSearchEngine;
use Domain\SearchEngineContext\Write\BusinessLogic\Gateways\SearchEngineGateway;

class DeleteObjectInSearchEngine
{
    public function __construct(
        private SearchEngineGateway $engine
    )
    {
    }

    public function __invoke(string $id, string $indexName)
    {
        $this->engine->setIndexName($indexName);
        $this->engine->delete($id);
    }
}
