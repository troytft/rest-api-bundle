benchmark:
	vendor/bin/phpbench run --report=aggregate --iterations=50

save-benchmark:
	vendor/bin/phpbench run --report=aggregate --iterations=50 --store

benchmark-history:
	vendor/bin/phpbench log
