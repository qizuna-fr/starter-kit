<?php

namespace App\Service\SearchEngine;

use App\Entity\DeviceReference;
use Meilisearch\Client;
use Symfony\Component\Serializer\SerializerInterface;

use function get_class;
use function str_replace;
use function strtolower;

final class SearchEngineIndexer
{
    public const SERIALIZING_GROUP = 'search_engine';


    public function __construct(private Client $client, private SerializerInterface $serializer)
    {
    }

    public function indexOne(mixed $object)
    {
        $json = $this->serializer->serialize($object, 'json', ['groups' => self::SERIALIZING_GROUP]);
        $className = get_class($object);
        $this->client
            ->index("techeaux_" . str_replace("\\", "_", strtolower($className)))
            ->addDocumentsJson($json);
    }

    public function indexMultiple(array $objects)
    {
        foreach ($objects as $object) {
            $this->indexOne($object);
        }
    }

    public function removeAllIndexes()
    {

        $indexes = $this->client->getIndexes();
        foreach ($indexes as $index) {
            $this->client->deleteIndex($index->getUid());
        }
    }
}
