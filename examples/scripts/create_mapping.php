<?php

/** @var \Elasticsearch\Client $client */
list($rulerz, $client) = require_once __DIR__.'/../bootstrap.php';

echo "\e[32mDeleting index 'rulerz_tests'\e[0m\n";

try {
    $client->indices()->delete(['index' => 'rulerz_tests']);
} catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
    echo "Index did not exist\n";
}

echo "\e[32mCreating index 'rulerz_tests' (with mapping)\e[0m\n";

$client->indices()->create([
    'index' => 'rulerz_tests',
    'body' => [
        'mappings' => [
            'player' => [
                'properties' => [
                    'pseudo' => [
                        'type' => 'keyword',
                    ],
                    'gender' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
        ],
    ],
]);
