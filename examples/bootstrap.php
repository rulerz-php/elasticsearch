<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$client = new Elasticsearch\Client([
    'hosts' => ['localhost:9200'],
]);

// compiler
$compiler = \RulerZ\Compiler\Compiler::create();

// RulerZ engine
$rulerz = new \RulerZ\RulerZ(
    $compiler, [
        new \RulerZ\Target\Elasticsearch\Elasticsearch(),
    ]
);

return [$rulerz, $client];
