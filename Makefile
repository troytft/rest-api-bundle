benchmark:
	vendor/bin/phpbench run --report=aggregate

benchmark-compare:
	vendor/bin/phpbench run --report=aggregate --ref=original

benchmark-save:
	vendor/bin/phpbench run --tag=original

php-cs-fixer:
	vendor/bin/php-cs-fixer fix . --config=php-cs-fixer.php
