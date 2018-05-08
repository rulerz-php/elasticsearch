<?php

declare(strict_types=1);

namespace RulerZ\Elasticsearch\Target;

use Elasticsearch\Client;

use RulerZ\Compiler\Context;
use RulerZ\Elasticsearch\Executor\FilterTrait;
use RulerZ\Executor\Polyfill\FilterBasedSatisfaction;
use RulerZ\Target\AbstractCompilationTarget;
use RulerZ\Target\Operators\Definitions;

class Elasticsearch extends AbstractCompilationTarget
{
    /**
     * {@inheritdoc}
     */
    public function supports($target, string $mode): bool
    {
        return $target instanceof Client;
    }

    /**
     * {@inheritdoc}
     */
    protected function createVisitor(Context $context)
    {
        return new ElasticsearchVisitor($this->getOperators());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutorTraits()
    {
        return [
            FilterTrait::class,
            FilterBasedSatisfaction::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getOperators(): Definitions
    {
        return Operators\Definitions::create(parent::getOperators());
    }
}
