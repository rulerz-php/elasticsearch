<?php

declare(strict_types=1);

namespace RulerZ\Elasticsearch\Executor;

use RulerZ\Context\ExecutionContext;

trait FilterTrait
{
    // just because traits can not have constants
    private static $DEFAULT_CHUNK_SIZE = 50;

    private static $DEFAULT_SCROLL_DURATION = '30s';

    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritdoc}
     */
    public function applyFilter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        return $this->execute($target, $operators, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var array $searchQuery */
        $searchQuery = $this->execute($target, $operators, $parameters);

        /** @var \Elasticsearch\Client $target */
        $results = $target->search([
            'index' => $context['index'],
            'type' => $context['type'],
            'scroll' => $context->get('scroll_duration', self::$DEFAULT_SCROLL_DURATION),
            'size' => $context->get('chunks_size', self::$DEFAULT_CHUNK_SIZE),
            'body' => ['query' => $searchQuery],
        ]);

        while (true) {
            if (empty($results['hits']['hits'])) {
                break;
            }

            foreach ($results['hits']['hits'] as $result) {
                yield $result['_source'];
            }

            $results = $target->scroll([
                'scroll_id' => $scrollId = $results['_scroll_id'],
                'scroll' => $context->get('scroll_duration', self::$DEFAULT_SCROLL_DURATION),
            ]);
        }
    }
}
