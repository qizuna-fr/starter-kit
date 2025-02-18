<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\Adapters\Secondary;


use Domain\SearchEngineContext\Write\BusinessLogic\Gateways\SearchEngineGateway;

class FakeSearchEngine implements SearchEngineGateway
{
    public array $indexes = [];
    public bool $createCalled = false;
    public bool $updateCalled = false;
    public bool $deleteCalled = false;

    public function __construct(
        private ?string $indexName = null
    ) {
    }



    public function create(string $id, array $jsonData = []): void
    {
        $this->indexes[$this->getIndexName()][$id] = $jsonData;
        $this->createCalled = true;
    }

    public function update(string $id,  array $jsonData = []): void
    {
        $this->indexes[$this->getIndexName()][$id] = $jsonData;
        $this->updateCalled = true;
    }

    public function delete(string $id): void
    {
        unset($this->indexes[$this->getIndexName()][$id]);
        $this->deleteCalled = true;
    }

    public function setIndexName(string $indexName): SearchEngineGateway
    {
        $this->indexName = $indexName;
        return $this;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function deleteIndex(string $index): void
    {
        // TODO: Implement deleteIndex() method.
    }
}
