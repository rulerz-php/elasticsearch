<?php

declare(strict_types=1);

namespace Tests\RulerZ\Target;

use Elasticsearch\Client;
use PHPUnit\Framework\TestCase;
use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Elasticsearch\Target\Elasticsearch;
use RulerZ\Exception\OperatorNotFoundException;
use RulerZ\Model\Executor;
use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;

class ELasticsearchTest extends TestCase
{
    private $target;

    public function setUp()
    {
        $this->target = new Elasticsearch();
    }

    /**
     * @dataProvider supportedTargetsAndModes
     */
    public function testSupportedTargetsAndModes($target, string $mode): void
    {
        $this->assertTrue($this->target->supports($target, $mode));
    }

    public function supportedTargetsAndModes(): array
    {
        $client = $this->createMock(Client::class);

        return [
            [$client, CompilationTarget::MODE_APPLY_FILTER],
            [$client, CompilationTarget::MODE_FILTER],
            [$client, CompilationTarget::MODE_SATISFIES],
        ];
    }

    /**
     * @dataProvider unsupportedTargets
     */
    public function testItRejectsUnsupportedTargets($target)
    {
        $this->assertFalse($this->target->supports($target, CompilationTarget::MODE_FILTER));
    }

    public function unsupportedTargets(): array
    {
        return [
            ['string'],
            [42],
            [new \stdClass()],
            [[]],
        ];
    }

    public function testItReturnsAnExecutorModel()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertInstanceOf(Executor::class, $executorModel);
        $this->assertCount(2, $executorModel->getTraits());
    }

    public function testItCompilesASimpleRule()
    {
        $rule = 'points > 30';
        $expectedQuery = <<<'QUERY'
[
    'bool' => ['must' => [
                'range' => [
                    'points' => ['gt' => 30],
                ]
            ]]
]
QUERY;

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame($expectedQuery, $executorModel->getCompiledRule());
    }

    public function testItHandlesParameters()
    {
        $rule = 'points > :total';
        $expectedQuery = <<<'QUERY'
[
    'bool' => ['must' => [
                'range' => [
                    'points' => ['gt' => $parameters["total"]],
                ]
            ]]
]
QUERY;

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame($expectedQuery, $executorModel->getCompiledRule());
    }

    public function testItHandlesNestedAccesses()
    {
        $rule = 'user.stats.points > 30';
        $expectedQuery = <<<'QUERY'
[
    'bool' => ['must' => [
                'range' => [
                    'user.stats.points' => ['gt' => 30],
                ]
            ]]
]
QUERY;

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame($expectedQuery, $executorModel->getCompiledRule());
    }

    public function testItThrowsAnErrorIfAnUnknownOperatorIsCalled()
    {
        $this->expectException(OperatorNotFoundException::class);

        $this->target->compile($this->parseRule('operator_that_does_not_exist() = 42'), new Context());
    }

    private function parseRule(string $rule): Rule
    {
        return (new Parser())->parse($rule);
    }
}