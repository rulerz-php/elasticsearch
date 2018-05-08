<?php

declare(strict_types=1);

namespace RulerZ\Elasticsearch\Target\Operators;

use RulerZ\Target\Operators\Definitions as RulerzDefinitions;

class Definitions
{
    public static function create(RulerzDefinitions $customOperators): RulerzDefinitions
    {
        $definitions = new RulerzDefinitions();

        // start with a few helpers
        $must = function ($query) {
            return "[
    'bool' => ['must' => $query]
]";
        };
        $mustNot = function ($query) {
            return "[
                'bool' => ['must_not' => $query]
            ]";
        };
        $range = function ($field, $value, $operator) use ($must) {
            return $must("[
                'range' => [
                    '$field' => ['$operator' => $value],
                ]
            ]");
        };

        // Here are the operators!
        $definitions->defineInlineOperator('and', function ($a, $b) use ($must) {
            return $must("[$a, $b]");
        });
        $definitions->defineInlineOperator('or', function ($a, $b) use ($must) {
            return "[
                'bool' => ['should' => [$a, $b], 'minimum_should_match' => 1]
            ]";
        });

        $definitions->defineInlineOperator('like', function ($a, $b) use ($must) {
            $value = is_array($b) ? implode(' ', $b) : $b;

            return $must("[
                'match' => [
                    '$a' => '$value',
                ]
            ]");
        });
        $definitions->defineInlineOperator('has', function ($a, $b) use ($must) {
            $value = is_array($b) ? '['.implode(', ', $b).']' : $b;

            return $must("[
                'terms' => [
                    '$a' => $value,
                ]
            ]");
        });
        $definitions->defineInlineOperator('in', $definitions->getInlineOperator('has'));

        $definitions->defineInlineOperator('=', function ($a, $b) use ($must) {
            return $must("[
                'term' => [
                    '$a' => $b,
                ]
            ]");
        });

        $definitions->defineInlineOperator('!=', function ($a, $b) use ($mustNot) {
            return $mustNot("[
                'term' => [
                    '$a' => $b,
                ]
            ]");
        });

        $definitions->defineInlineOperator('not', $mustNot);

        $definitions->defineInlineOperator('>', function ($a, $b) use ($range) {
            return $range($a, $b, 'gt');
        });

        $definitions->defineInlineOperator('>=', function ($a, $b) use ($range) {
            return $range($a, $b, 'gte');
        });

        $definitions->defineInlineOperator('<', function ($a, $b) use ($range) {
            return $range($a, $b, 'lt');
        });

        $definitions->defineInlineOperator('<=', function ($a, $b) use ($range) {
            return $range($a, $b, 'lte');
        });

        return $definitions->mergeWith($customOperators);
    }
}
