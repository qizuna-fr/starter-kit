<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Domain\SearchEngineContext\Write\Adapters\Primary\Listeners;



use Domain\SearchEngineContext\Write\BusinessLogic\Events\CreatedEventInterface;
use Domain\SearchEngineContext\Write\BusinessLogic\Events\DeletedEventInterface;
use Domain\SearchEngineContext\Write\BusinessLogic\Events\UpdatedEventInterface;
use Domain\SearchEngineContext\Write\BusinessLogic\UseCases\DeleteObjectInSearchEngine;
use Domain\SearchEngineContext\Write\BusinessLogic\UseCases\IndexObjectInSearchEngine;
use Domain\SearchEngineContext\Write\BusinessLogic\UseCases\UpdateObjectInSearchEngine;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use function json_encode;

#[AsEventListener(event: CreatedEventInterface::class, method: 'onCreate')]
#[AsEventListener(event: UpdatedEventInterface::class, method: 'onUpdate')]
#[AsEventListener(event: DeletedEventInterface::class, method: 'onDelete')]
class ExampleCustomerListener
{

    const INDEX_NAME = 'Customer';

    public function __construct(
        private IndexObjectInSearchEngine $indexObjectInSearchEngine,
        private UpdateObjectInSearchEngine $updateObjectInSearchEngine,
        private DeleteObjectInSearchEngine $deleteObjectInSearchEngine
    ) {
    }

    public function onCreate(CreatedEventInterface $event)
    {
        $this->indexObjectInSearchEngine->__invoke(
            $event->getId(),
            $event->getIndexName(),
            json_encode($event->getPayload())
        );
    }

    public function onDelete(DeletedEventInterface $event)
    {
        $this->deleteObjectInSearchEngine->__invoke($event->getId(), self::INDEX_NAME);
    }


    public function onUpdate(UpdatedEventInterface $event)
    {
        $this->updateObjectInSearchEngine->__invoke(
            $event->getId(),
            $event->getIndexName(),
            json_encode($event->getPayload())
        );
    }



}
