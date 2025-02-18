<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways;


use Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch\Result\SearchResult;
//use Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch\SearchEngineRequester;
use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\ReadFromSearchEngineGateway;
use Meilisearch\Client;

class MeilisearchEngineGateway implements ReadFromSearchEngineGateway
{

    public function __construct(
        //private SearchEngineRequester $requester,
        private Client $client,
        private string $meilisearchPrefix = 'prefix'
    )
    {
    }

    /**
     * @param string $query
     * @return array<?SearchResult>
     */
    public function executeQuery(string $query): array
    {
        //$requests = $this->requester->buildRequest($query, 10);
        $results = $this->client->multiSearch($requests);

        $resultsArray = [];

        foreach ($results as $searches) {
            foreach ($searches as $collection) {
                $type = $collection['indexUid'];
                $hits = $collection['hits'];

                foreach ($hits as $hit){
                    $resultsArray[] = new SearchResult($this->meilisearchPrefix, $type, $hit);
                };
            }
        }

        return $resultsArray;

    }
}
