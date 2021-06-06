benchmark:
	vendor/bin/phpbench run --report=aggregate --iterations=20

save-benchmark:
	vendor/bin/phpbench run --report=aggregate --iterations=20 --store

benchmark-history:
	vendor/bin/phpbench log
