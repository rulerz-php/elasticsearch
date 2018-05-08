#!/bin/sh

echo "\e[32mDeleting 'rulerz_tests'\e[0m"
curl -X DELETE http://localhost:9200/rulerz_tests

echo "\n\e[32mCreating mapping for 'rulerz_tests'\e[0m"
curl -X POST http://localhost:9200/rulerz_tests -d '{
  "mappings": {
    "player": {
      "properties": {
        "pseudo":   { "type": "string", "index":  "not_analyzed" },
        "gender":   { "type": "string", "index":  "not_analyzed" }
      }
    }
  }
}'

echo ""