tests: unit behat

release:
	./vendor/bin/RMT release

unit:
	php ./vendor/bin/phpunit

behat:
	php ./vendor/bin/behat --colors -vvv

database:
	php ./examples/scripts/create_mapping.php && php ./examples/scripts/load_fixtures.php

rusty:
	php ./vendor/bin/rusty check --no-execute README.md

elasticsearch_start:
	docker run -d -p 9200:9200 -e "discovery.type=single-node" --name es-rulerz docker.elastic.co/elasticsearch/elasticsearch:6.4.1

elasticsearch_stop:
	docker rm -f es-rulerz
