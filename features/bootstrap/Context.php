<?php

declare(strict_types=1);

use Behat\Behat\Context\Context as BehatContext;
use RulerZ\Test\BaseContext;

class Context extends BaseContext implements BehatContext
{
    /** @var \Elasticsearch\Client */
    private $client;

    public function initialize()
    {
        $this->client = \Elasticsearch\ClientBuilder::create()->build();
    }

    /**
     * {@inheritdoc}
     */
    protected function getCompilationTarget(): \RulerZ\Compiler\CompilationTarget
    {
        return new \RulerZ\Elasticsearch\Target\Elasticsearch();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultDataset()
    {
        return $this->client;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExecutionContext(): array
    {
        return [
            'index' => 'rulerz_tests',
            'type' => 'player',
        ];
    }
}
