test:
	vendor/bin/phpunit
	vendor/bin/phpcs
	vendor/bin/phpstan analyse src tests --level=5 --no-progress
