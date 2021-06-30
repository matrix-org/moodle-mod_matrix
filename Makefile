.PHONY: it
it: coding-standards ## Runs the coding-standards target

.PHONY: coding-standards
coding-standards: vendor ## Normalizes composer.json with ergebnis/composer-normalize and fixes code style issues with friendsofphp/php-cs-fixer
	./composer.phar normalize
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --verbose

vendor: composer.json composer.lock
	./composer.phar validate --strict
	./composer.phar install --no-interaction --no-progress
