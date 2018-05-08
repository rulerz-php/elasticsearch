<?php

declare(strict_types=1);

namespace Tests\RulerZ\Executor;

use Elasticsearch\Client;
use PHPUnit\Framework\TestCase;
use RulerZ\Context\ExecutionContext;
use Tests\RulerZ\Stub\ElasticsearchExecutorStub;

class FilterTraitTest extends TestCase
{
    private $executor;

    public function setUp()
    {
        $this->executor = new ElasticsearchExecutorStub();
    }

    public function testItCanApplyAFilterOnATarget()
    {
        $target = $this->createMock(Client::class);
        $esQuery = ['array with the ES query'];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;
        $target->expects($this->never())->method('search');

        $result = $this->executor->applyFilter($target, $parameters = [], $operators = [], new ExecutionContext());

        $this->assertSame($esQuery, $result);
    }

    public function testItCallsSearchOnTheTarget()
    {
        $target = $this->createMock(Client::class);

        $documents = [
            'first document',
            'other document',
        ];
        $result = [
            '_scroll_id' => 'some-scroll-id',
        ];
        $esQuery = ['array with the ES query'];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;

        $target->method('search')->with([
            'index' => 'es_index',
            'type' => 'es_type',
            'search_type' => 'scan',
            'scroll' => '30s',
            'size' => 50,
            'body' => ['query' => $esQuery],
        ])->willReturn($result);

        $target->method('scroll')->with([
            'scroll_id' => 'some-scroll-id',
            'scroll' => '30s',
        ])->willReturnOnConsecutiveCalls([
            '_scroll_id' => 'some-scroll-id',
            'hits' => [
                'total' => 1,
                'hits' => [
                    ['_source' => 'first document'],
                    ['_source' => 'other document'],
                ],
            ],
        ], [
            '_scroll_id' => 'some-scroll-id',
            'hits' => [
                'total' => 1,
                'hits' => [],
            ],
        ]);

        $results = $this->executor->filter($target, $parameters = [], $operators = [], new ExecutionContext([
            'index' => 'es_index',
            'type' => 'es_type',
        ]));

        $this->assertInstanceOf(\Generator::class, $results);
        $this->assertSame($documents, iterator_to_array($results));
    }
}
