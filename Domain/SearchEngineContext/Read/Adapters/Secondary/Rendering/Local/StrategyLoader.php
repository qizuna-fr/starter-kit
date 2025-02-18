<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Rendering\Local;


use Domain\SearchEngineContext\Read\Adapters\Secondary\Rendering\Local\Strategies\CustomerSearchResultStrategy;
use Domain\SearchEngineContext\Read\Adapters\Secondary\Rendering\Local\Strategies\ProjectSearchResultStrategy;
use Domain\SearchEngineContext\Read\Adapters\Secondary\Rendering\Local\Strategies\PublicMarketSearchResultStrategy;
use Domain\SearchEngineContext\Read\BusinessLogic\Rendering\SearchResultStrategy;

use function strtolower;

class StrategyLoader
{
    private array $strategies = [
        'customer' => CustomerSearchResultStrategy::class,
        'publicmarket' => PublicMarketSearchResultStrategy::class,
    ];

    public function getStrategy(string $strategy): ?SearchResultStrategy
    {
        $class = $this->strategies[strtolower($strategy)] ?? null;
        if ($class === null) {
            return null;
        }
        return new $class();
    }
}
