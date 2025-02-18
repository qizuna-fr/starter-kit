<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Read\Adapters\Secondary\Gateways\Meilisearch\Result;


use function str_replace;

class SearchResult
{

    public function __construct(
        private string $prefix,
        private string $type,
        private ?array $payload
    )
    {
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getType(): string
    {
        return str_replace($this->getPrefix(), '', (string)$this->type);
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }


}
