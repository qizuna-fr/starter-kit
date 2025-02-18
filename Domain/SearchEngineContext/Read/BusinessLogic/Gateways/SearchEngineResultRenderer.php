<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\BusinessLogic\Gateways;

use Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch\Result\SearchResult;

interface SearchEngineResultRenderer
{
    /**
     * @param array<SearchResult> $results
     * @return void
     */
    public function setResults(array $results): void;

    public function renderAll();
}
