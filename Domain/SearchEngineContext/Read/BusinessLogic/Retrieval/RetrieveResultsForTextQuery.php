<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\BusinessLogic\Retrieval;


class RetrieveResultsForTextQuery
{

    public function __construct(
        private string $queryString
    )
    {
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }


}
