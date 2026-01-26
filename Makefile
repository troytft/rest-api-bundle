composer-install:
	composer install

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
	vendor/bin/phpunit --display-warnings --display-deprecations --display-notices --testdox

unit-test-fix:
	vendor/bin/phpunit -d --update-snapshots
