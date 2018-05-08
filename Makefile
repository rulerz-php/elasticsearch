tests: unit behat

release:
	./vendor/bin/RMT release

unit:
	php ./vendor/bin/phpunit

behat:
	php ./vendor/bin/behat --colors -vvv

database:
	./examples/scripts/create_mapping.sh && php ./examples/scripts/load_fixtures.php

rusty:
	php ./vendor/bin/rusty check --no-execute README.md

elasticsearch_start:
	docker run -d -p 9200:9200 --name es-rulerz elasticsearch:2.4

elasticsearch_stop:
	docker rm -f es-rulerz