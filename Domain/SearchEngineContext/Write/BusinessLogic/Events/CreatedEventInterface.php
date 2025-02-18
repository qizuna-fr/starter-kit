<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\BusinessLogic\Events;

interface CreatedEventInterface
{
    public function getId(): string;

    public function getPayload(): mixed;

    public function getIndexName(): string;
}
