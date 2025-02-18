<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch;


use Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch\Result\SearchResult;
use Domain\SearchEngineContext\Read\Adapters\Secondary\Rendering\Local\StrategyLoader;
use Domain\SearchEngineContext\Read\BusinessLogic\Gateways\SearchEngineResultRenderer;
use Twig\Environment;

use function implode;

class LocalSearchEngineResultRenderer implements SearchEngineResultRenderer
{
    /** @var array<SearchResult>  */
    private array $results = [];
    private array $renderings = [];

    public function __construct(
        private Environment $twig
    )
    {

    }

    public function setResults(array $results): void
    {
        $this->results = $results;
    }

    public function renderAll()
    {
        $strategyLoader = new StrategyLoader();

        $this->renderings = [];

        foreach ($this->results as $result) {

            $strategy = $strategyLoader->getStrategy($result->getType());

            if($strategy === null) {
                continue;
            }

            $this->renderings[] = $this->twig->render(
                $strategy->getTemplate(),
                ['item' => $result->getPayload()]
            ) ;
        }

        return implode("", $this->renderings);

    }
}
