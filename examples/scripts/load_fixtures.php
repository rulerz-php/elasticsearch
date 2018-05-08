<?php

/** @var \Elasticsearch\Client $client */
list($rulerz, $client) = require_once __DIR__.'/../bootstrap.php';

$fixtures = json_decode(file_get_contents(__DIR__.'/../../vendor/kphoen/rulerz/examples/fixtures.json'), true);

echo sprintf("\e[32mLoading fixtures for %d players\e[0m", count($fixtures['players']));

foreach ($fixtures['players'] as $player) {
    $params = [
        'body' => [
            'pseudo' => $player['pseudo'],
            'fullname' => $player['fullname'],
            'birthday' => $player['birthday'],
            'gender' => $player['gender'],
            'points' => $player['points'],
        ],
        'index' => 'rulerz_tests',
        'type' => 'player',
        'id' => uniqid(),
    ];
    $client->index($params);
}
