<?php

namespace Infrastructure\Factories;

use Meilisearch\Client;
use Symfony\Component\Yaml\Yaml;

use function explode;

final class MeilisearchFactory
{
    public static function createMeilisearchClient(string $url, ?string $masterKey = null, $stopWords = null): Client
    {

        $client =  new Client($url, $masterKey);
        $stopwords = explode(",", $stopWords);

        $yaml = Yaml::parseFile(__DIR__ . "/../../config/meilisearch/settings.yaml");

        foreach ($client->getIndexes() as $index) {
            $client->index($index->getUid())->updateStopWords($stopwords);

            if (!isset($yaml[$index->getUid()])) {
                continue;
            }

            $client->getIndex($index->getUid())->updateSettings($yaml[$index->getUid()]);
        }

        return $client;
    }
}
