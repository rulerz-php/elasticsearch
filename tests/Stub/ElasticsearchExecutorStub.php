<?php

namespace Tests\RulerZ\Stub;

use RulerZ\Elasticsearch\Executor\FilterTrait;

class ElasticsearchExecutorStub
{
    public static $executeReturn;

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}
