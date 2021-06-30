.PHONY: it
it: coding-standards ## Runs the coding-standards target

.PHONY: coding-standards
coding-standards: vendor ## Normalizes composer.json with ergebnis/composer-normalize
	./composer.phar normalize

vendor: composer.json composer.lock
	./composer.phar validate --strict
	./composer.phar install --no-interaction --no-progress
