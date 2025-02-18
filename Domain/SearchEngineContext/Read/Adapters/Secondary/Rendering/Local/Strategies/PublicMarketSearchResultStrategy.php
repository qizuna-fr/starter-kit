<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Rendering\Local\Strategies;


use Domain\SearchEngineContext\Read\BusinessLogic\Rendering\SearchResultStrategy;

class PublicMarketSearchResultStrategy implements SearchResultStrategy
{

    public function getTemplate()
    {
        return 'partials/search_engine/publicmarket_result.html.twig';
    }
}
