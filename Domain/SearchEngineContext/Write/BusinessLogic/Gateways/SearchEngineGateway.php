<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\BusinessLogic\Gateways;

interface SearchEngineGateway
{
    public function setIndexName(string $indexName): SearchEngineGateway;
    public function getIndexName(): string;
    public function create(string $id, array $jsonData = []);
    public function update(string $id,  array $jsonData = []): void;
    public function delete(string $id ): void;
    public function deleteIndex(string $index): void;
}
