<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\BusinessLogic\Retrieval;



use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\ReadFromSearchEngineGateway;

class RetrieveResultsForTextHandler
{

    public function __construct(
        //private  CentralizedSearchEngineService $engineService
        private ReadFromSearchEngineGateway $searchEngineGateway
    )
    {
    }

    public function handle(RetrieveResultsForTextQuery $retrieveResultsForTextQuery){

        return $this->searchEngineGateway->executeQuery($retrieveResultsForTextQuery->getQueryString());

    }
}
