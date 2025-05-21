benchmark:
	vendor/bin/phpbench run --report=aggregate

benchmark-compare:
	vendor/bin/phpbench run --report=aggregate --ref=original

benchmark-save:
	vendor/bin/phpbench run --tag=original

test-cs:
	vendor/bin/php-cs-fixer fix src --config=php-cs-fixer.php --dry-run --diff

fix-cs:
	vendor/bin/php-cs-fixer fix src --config=php-cs-fixer.php

test-unit:
	vendor/bin/phpunit --testdox

save-unit:
	vendor/bin/phpunit -d --update-snapshots
