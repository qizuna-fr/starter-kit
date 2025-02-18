<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\Adapters\Secondary;


use Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\MeilisearchEngineGateway;
use Domain\SearchEngineContext\Write\BusinessLogic\Gateways\SearchEngineGateway;
use Meilisearch\Client;

use function array_map;
use function array_merge;
use function in_array;
use function json_encode;

class MeilisearchGateway implements SearchEngineGateway
{

    private string $prefixedMeiliSearchIndexName ='';

    public function __construct(
        private Client $meilisearchClient,
        private readonly MeilisearchEngineGateway $meilisearchEngineGateway,
        private ?string $meiliSearchIndexName = null,
        private ?string $meilisearchPrefix = null
    ) {

    }

    public function setIndexName(?string $indexName): MeilisearchGateway
    {
        $this->meiliSearchIndexName = $indexName;
        $this->prefixedMeiliSearchIndexName = $this->meilisearchPrefix.$indexName;
        return $this;
    }

    public function getIndexName(): string
    {
        return $this->meiliSearchIndexName;
    }

    public function getPrefixedIndexName(): string{

        return $this->prefixedMeiliSearchIndexName;
    }

    public function create(string $id, array $jsonData = [])
    {

        $data = ['id' => $id];
        $toIndexData = array_merge($data, $jsonData);

        return $this
            ->meilisearchClient
            ->index($this->prefixedMeiliSearchIndexName)
            ->addDocuments($toIndexData, 'id');

    }

    public function update(string $id, array $jsonData = []): void
    {
        $data = ['id' => $id];
        $data = array_merge($data, $jsonData);
        $this->meilisearchClient->index($this->prefixedMeiliSearchIndexName)->updateDocuments($data);
    }

    public function delete(string $id): void
    {
        $this->meilisearchClient->index($this->prefixedMeiliSearchIndexName)->deleteDocument($id);
    }


    public function deleteIndex(string $index): void
    {
        $indexes = array_map(fn($index) => $index->getUid(), $this->meilisearchClient->getIndexes()->getResults());

        if(in_array($index, $indexes)){
            $this->meilisearchClient->deleteIndex($index);
        }

    }


}
