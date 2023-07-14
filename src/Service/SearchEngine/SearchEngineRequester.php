<?php

namespace App\Service\SearchEngine;

use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Endpoints\Indexes;

use function array_map;
use function str_starts_with;

final class SearchEngineRequester
{
    private array $indexes;

    public function __construct(private Client $meilisearch)
    {
        $this->indexes = array_filter(
            $this->meilisearch->getIndexes()->getResults(),
            function ($index) {
                return str_starts_with($index->getUid(), 'techeaux');
            },
        );
    }

    public function buildRequest(string $query, int $maxResults)
    {
        $requests = [];

        //dd($this->indexes);

        /** @var Indexes $index */
        foreach ($this->indexes as $index) {
            $requests[] = (new SearchQuery())
                ->setIndexUid($index->getUid())
                ->setQuery($query)
                ->setLimit($maxResults);
        }

        return $requests;
    }
}
