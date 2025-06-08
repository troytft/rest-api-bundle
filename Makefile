benchmark:
	vendor/bin/phpbench run --report=aggregate

benchmark-compare:
	vendor/bin/phpbench run --report=aggregate --ref=original

benchmark-save:
	vendor/bin/phpbench run --tag=original

test-cs:
	vendor/bin/ecs check

fix-cs:
	vendor/bin/ecs check --fix

test-unit:
	vendor/bin/phpunit --testdox

save-unit:
	vendor/bin/phpunit -d --update-snapshots
