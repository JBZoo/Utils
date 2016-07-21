.PHONY: build test

build:
	composer self-update --no-interaction
	composer update --optimize-autoloader --no-interaction
	composer require symfony/process

test:
	composer phpunit

test-all:
	composer test
