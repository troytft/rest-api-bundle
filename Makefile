benchmark:
	vendor/bin/phpbench run --report=aggregate

benchmark-compare:
	vendor/bin/phpbench run --report=aggregate --ref=original

benchmark-save:
	vendor/bin/phpbench run --tag=original

cs-fixer:
	vendor/bin/
