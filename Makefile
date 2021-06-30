.PHONY: it
it: vendor ## Runs the vendor target

vendor: composer.json composer.lock
	./composer.phar install --no-interaction --no-progress
