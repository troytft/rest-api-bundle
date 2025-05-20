benchmark:
	vendor/bin/phpbench run --report=aggregate

benchmark-compare:
	vendor/bin/phpbench run --report=aggregate --ref=original

benchmark-save:
	vendor/bin/phpbench run --tag=original

test-cs:
	vendor/bin/php-cs-fixer fix src --dry-run --diff

fix-cs:
	vendor/bin/php-cs-fixer fix src
