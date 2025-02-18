<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways;


use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\ReadFromSearchEngineGateway;

use function array_filter;
use function array_values;
use function str_contains;

class FakeSearchEngine implements ReadFromSearchEngineGateway
{

    public array $indexes = [];

    public function executeQuery(string $query): ?array
    {
        $values =  array_values(
            array_filter(
                $this->indexes,
                fn(array $item) => str_contains($item['name'], $query)
            )
        );

        return $values;
    }
}
