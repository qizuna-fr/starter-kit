<?php

declare(strict_types=1);

namespace Domain\SearchEngineContext\Read\Adapters\Primary\Controllers;

use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\ReadFromSearchEngineGateway;
use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\SearchEngineResultRenderer;
use Domain\SearchEngineContext\Read\BusinessLogic\Retrieval\RetrieveResultsForTextHandler;
use Domain\SearchEngineContext\Read\BusinessLogic\Retrieval\RetrieveResultsForTextQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SearchController extends AbstractController
{
    public function __construct(
        private ReadFromSearchEngineGateway $searchEngineGateway,
        private SearchEngineResultRenderer $searchEngineRenderer
    ) {
    }

    #[Route('/search', name:'app_search_cities')]
    public function search(Request $request): Response
    {

        $query = new RetrieveResultsForTextQuery($request->get('q'));
        $handler = new RetrieveResultsForTextHandler($this->searchEngineGateway);

        $results = $handler->handle($query);

        $this->searchEngineRenderer->setResults($results);
        $html = $this->searchEngineRenderer->renderAll();

        return new Response($html, Response::HTTP_OK);

    }
}
