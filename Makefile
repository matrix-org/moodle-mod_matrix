.PHONY: it
it: vendor ## Runs the vendor target

vendor: composer.json composer.lock
	./composer.phar validate --strict
	./composer.phar install --no-interaction --no-progress
