<?php

declare(strict_types=1);

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch;

use Meilisearch\Client;
use Meilisearch\Contracts\SearchQuery;
use Meilisearch\Endpoints\Indexes;

use function array_filter;
use function str_starts_with;

final class SearchEngineRequester
{
    private array $indexes;

    public function __construct(private Client $meilisearch, private string $meilisearchPrefix)
    {
        $this->indexes = array_filter(
            $this->meilisearch->getIndexes()->getResults(),
            function ($index) {
                return str_starts_with($index->getUid(), $this->meilisearchPrefix);
            },
        );
    }

    public function buildRequest(string $query, int $maxResults)
    {
        $requests = [];

        $this->indexes = array_filter(
            $this->meilisearch->getIndexes()->getResults(),
            function ($index) {
                return str_starts_with($index->getUid(), $this->meilisearchPrefix);
            },
        );

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
