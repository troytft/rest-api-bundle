PHP_VERSION=8.4
COMPOSER_VERSION=2.9.4

composer-install:
	docker run --rm -v $(PWD):/app -w /app composer:$(COMPOSER_VERSION) install

benchmark:
	vendor/bin/phpbench run --report=aggregate

benchmark-compare:
	vendor/bin/phpbench run --report=aggregate --ref=original

benchmark-save:
	vendor/bin/phpbench run --tag=original

cs-test:
	vendor/bin/ecs check

cs-test-fix:
	vendor/bin/ecs check --fix

unit-test:
	docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION)-cli vendor/bin/phpunit --testdox

unit-test-fix:
	docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION)-cli vendor/bin/phpunit -d --update-snapshots
